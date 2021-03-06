<?php

namespace Lib;


use App\Repository\CategoryRepository;
use App\Repository\CommentRepository;
use App\Repository\PostRepository;
use Lib\Exceptions\BadRequestException;
use Lib\Exceptions\NotAuthorizedException;
use Lib\Exceptions\TokenNotValidException;
use Swift_Mailer;
use Swift_SmtpTransport;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;
use Twig\Environment;
use Twig\Extension\DebugExtension;
use Twig\Loader\FilesystemLoader;
use Twig\TwigFilter;
use Twig\TwigFunction;


abstract class AbstractController
{
    protected Request $request;

    protected Session $session;


    public function __construct(Request $request, Session $session)
    {
        $this->request = $request;

        $this->session = $session;
    }


    /**
     *  Twig render system
     *
     * @param string $view
     * @param array $params
     * @return Response
     */
    public function render(string $view, array $params): Response
    {
        $loader = new FilesystemLoader(__DIR__ . '/../templates');
        $twig = new Environment($loader, [
            'cache' => false,
            'debug' => true
        ]);

        $twig->addFunction(new TwigFunction('asset', function ($asset) {
            return sprintf('/assets/%s', ltrim($asset, '/'));
        }));

        $twig->addFilter(new TwigFilter('remove_accent', function ($string) {
            $slug = iconv('UTF-8', 'ASCII//TRANSLIT', $string);
            $slug = preg_replace("/([^a-z0-9]+)/", "", $slug);
            return $slug;
        }));

        $twig->addExtension(new DebugExtension());

        $twig->addGlobal('session', $this->session->all());
        $twig->addGlobal('flash', $this->session->getFlashBag()->all());
        $twig->addGlobal('get', $this->request->query->all());

        $commentManager = new CommentRepository();
        $twig->addGlobal('reported', $commentManager->countReported());

        $categories = new CategoryRepository();
        $twig->addGlobal('global_categories', $categories->findAll());

        $sidebar = new PostRepository();
        $twig->addGlobal('countPost', $sidebar->countItems('post'));
        $twig->addGlobal('countComment', $sidebar->countItems('comment'));
        $twig->addGlobal('countCategory', $sidebar->countItems('category'));
        $twig->addGlobal('countTags', $sidebar->countItems('tags'));
        $twig->addGlobal('countUser', $sidebar->countItems('user'));

        $render = $twig->render($view, $params);

        return new Response($render, 200, []);
    }


    /**
     * Redirection
     *
     * @param string $uri
     */
    public function redirectToRoute(string $uri)
    {
        header('Location: '.$uri);
    }


    /**
     * Remove special characters and format a slug
     *
     * @param string $title
     * @return string
     */
    public function formatSlug(string $title) : string
    {
        $slug = iconv('UTF-8', 'ASCII//TRANSLIT', $title);
        $slug = strtolower($slug);
        $slug = preg_replace("/([^a-z0-9]+)/", "-", $slug);
        $slug = $slug . '-';
        $slug = preg_replace("/-{2,}/", "-", $slug);

        return $slug;
    }


    /**
     * Upload File
     *
     * @param UploadedFile $uploadedFile
     * @return string
     * @throws BadRequestException
     */
    public function uploadFile(UploadedFile $uploadedFile) : string
    {
        $directory = __DIR__.'/../public/upload/';
        $mime = ['image/png', 'image/gif', 'image/jpg', 'image/jpeg'];
        $maxSize = 3000000;

        $file = $uploadedFile;
        $fileName = uniqid('upload-', TRUE).'.'.$file->getClientOriginalExtension();
        $fileMime = $file->getClientMimeType();
        $fileSize = $file->getSize();

        // Check mime type
        if(!in_array($fileMime, $mime))
        {
            throw new BadRequestException('Le fichier n\'est pas une image.', 400);
        }

        // Check file size
        if($fileSize > $maxSize)
        {
            throw new BadRequestException('Le fichier est trop volumineux (3mo max)', 400);
        }

        // Upload
        if(!$file->move($directory, $fileName))
        {
            throw new BadRequestException('Upload du fichier échoué', 400);
        }

        return $fileName;
    }


    /**
     * Check roles
     *
     * @throws NotAuthorizedException
     */
    public function checkIfAdmin()
    {
        if($this->session->get('user')->getRole() != 'ADMIN')
        {
            throw new NotAuthorizedException('Vous n\'avez pas les droits pour effectuer cette opération', 401);
        }
    }


    /**
     * Check if user is connected
     *
     * @throws NotAuthorizedException
     */
    public function checkIfConnected()
    {
        if($this->session->get('user') === null)
        {
            throw new NotAuthorizedException('Vous n\'êtes pas connecté !', 401);
        }
    }


    /**
     * Set token csrf
     */
    public function setTokenCsrf() : void
    {
        $this->session->set('csrf_token', md5(bin2hex(openssl_random_pseudo_bytes(8))));
    }


    /**
     * Check token csrf
     *
     * @throws TokenNotValidException
     */
    public function checkTokenCsrf()
    {
        if($this->request->request->get('csrf_token') != $this->session->get('csrf_token'))
        {
            throw new TokenNotValidException('Token expiré', 498);
        }
    }


    /**
     * Mailer
     *
     * @return Swift_Mailer
     */
    public function mailer() : Swift_Mailer
    {
        $transport = (new Swift_SmtpTransport($_ENV['MAILER_HOST'], 465, 'ssl'))
            ->setUsername($_ENV['MAILER_USERNAME'])
            ->setPassword($_ENV['MAILER_PASSWORD'])
        ;

        return new Swift_Mailer($transport);
    }
}