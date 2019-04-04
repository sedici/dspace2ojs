<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\User\UserInterface;

class FileController extends AbstractController
{
    /**
     * @Route("/file", name="file")
     */
    public function index()
    {
        return $this->render('file/index.html.twig', [
            'controller_name' => 'FileController',
        ]);
    }
     /**
     * @Route("/file/my-files", name="my_files")
     */
    public function myFiles(UserInterface $current_user)
     {
        $files=$current_user->getFiles();
        $setting_files=array();
        foreach ($files as $value) {
            if(!in_array($value->getSettingFile(),$setting_files))
                $setting_files[] = $value->getSettingFile();
        }
        return $this->render('file/my_files.html.twig', [
            'files' => $current_user->getFiles(), 'setting_files' => $setting_files
        ]);
    }
}
