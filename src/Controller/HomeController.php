<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Form\CsvType;
use App\Service\DSpace2OJSService;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\HttpFoundation\JsonResponse;
use App\Entity\File;
use Doctrine\ORM\EntityManager;

class HomeController extends AbstractController
{
    /**
     * @Route("/home", name="home")
     */
    public function index(Request $request, UserInterface $current_user, DSpace2OJSService $csv2xml)
    {
        // $csv2xml=new DSpace2OJSService();
        $form = $this->createForm(CsvType::class);
        $form->handleRequest($request);
        if ($form->isSubmitted()) {
            $file = $request->files->get('csv')['csv_file'];
            // dump($request->get('csv')['into_section']);die;
            $path_info = pathinfo($file->getClientOriginalName());
            $params = $request->get('csv');
            $fileName = $path_info['filename'] . '.' . $path_info['extension'];
            $fileDir = 'files/user_' . $current_user->getId() . '/' . $path_info['filename'];
            try {
                $file->move(
                    $fileDir,
                    $fileName
                );
                // split csv file by publication number
                $files = $csv2xml->splitFileIntoMultipleCSV($fileDir . '/', $path_info['filename'], $request->get('csv')['into_section'], $request->get('csv')['authors_group'], $request->get('csv')['limit']);
                return $this->redirectToRoute('my_files');
                // $csv2xml->processFiles($files,$params);
            } catch (FileException $e) {
                // ... handle exception if something happens during file upload
            }
        }
        return $this->render('home/index.html.twig', [
            'controller_name' => 'HomeController', 'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/home/process_file", name="process-file")
     */
    public function processFile(Request $request, DSpace2OJSService $csv2xml)
    {

        $data = $request->query->get('data');
        $doctrine = $this->getDoctrine();
        $em = $doctrine->getManager();
        $file_repo = $em->getRepository(File::class);
        $file = $file_repo->findOneBy(array('path' => $data));
        $fileName = $csv2xml->processFile($data, $file->getSettingFile()->getIntoSection(),$file->getSettingFile()->getAuthorsGroup(), $file->getSettingFile()->getLimitFiles());
        if ($fileName) {

            $file->setConverted(true);
            $em->persist($file);
            $em->flush();
        }
        $encoders = array(new JsonEncoder());
        $normalizers = array(new ObjectNormalizer());
        $serializer = new Serializer($normalizers, $encoders);
        $response = new JsonResponse();
        $response->setStatusCode(200);
        $response->setData(array(

            'response' => $fileName
        ));
        return $response;
    }
}
