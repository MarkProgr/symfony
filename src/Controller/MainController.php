<?php

namespace App\Controller;

use App\Filesystem\FileUploader;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MainController extends AbstractController
{
    #[Route('/main', name: 'app_main', methods: ['GET'])]
    public function index(): Response
    {
        return $this->render('base.html.twig');
    }

    #[Route('/main', name: 'app_main_upload', methods: ['POST'])]
    public function upload(Request $request, FileUploader $uploader): RedirectResponse
    {
        /**
 * @var UploadedFile $file
*/
        $file = $request->files->get('file');

        $uploader->uploadFile(
            sha1($file->getContent()) . '.' . $file->guessExtension(),
            $file->getContent()
        );

        return $this->redirectToRoute('app_main');
    }
}
