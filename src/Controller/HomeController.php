<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Form\CsvType;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use App\Service\DSpace2OJSService;
use App\Command\DspaceOjsCommand;
// use Symfony\Component\Console\Application;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\BufferedOutput;
use Symfony\Component\HttpKernel\KernelInterface;

class HomeController extends AbstractController
{
    /**
     * @Route("/home", name="home")
     */
    public function index(Request $request, UserInterface $current_user,DSpace2OJSService $csv2xml)
    {
        // $csv2xml=new DSpace2OJSService();
        $form= $this->createForm( CsvType::class);
        $form->handleRequest($request);
        if($form->isSubmitted() ){
            $file=$request->files->get('csv')['csv_file'];
            $path_info=pathinfo($file->getClientOriginalName());
            $params=$request->get('csv');
            $fileName = $path_info['filename'].'.'.$path_info['extension'];
            $fileDir='files/user_'.$current_user->getId().'/'.$path_info['filename'];
            try {
                $file->move(
                    $fileDir,
                    $fileName
                );
                // split csv file by publication number
                $files=$csv2xml->splitFileIntoMultipleCSV($fileDir.'/'.$path_info['filename']);
                $csv2xml->processFiles($files,$params);
            } catch (FileException $e) {
                // ... handle exception if something happens during file upload
            }
            
        }
        return $this->render('home/index.html.twig', [
            'controller_name' => 'HomeController' , 'form'=> $form->createView() ,
        ]);
    }
    /**
     * @Route("/test", name="test")
     */
    public function testExecute( KernelInterface $kernel)
    {
        $application = new Application($kernel);
        $application->setAutoExit(false);

        $input = new ArrayInput([
           'command' => 'app:dspaceojs',
           // (optional) define the value of command arguments
           'filename' => 'files/user_4/10915-837/10915-836',
           // (optional) pass options to the command
        ]);

        // You can use NullOutput() if you don't need the output
        $output = new BufferedOutput();
        $application->run($input, $output);
        
        return new Response();
    }
}
