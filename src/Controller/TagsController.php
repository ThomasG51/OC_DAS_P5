<?php


namespace App\Controller;


use App\Repository\TagsRepository;
use Lib\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class TagsController extends AbstractController
{
    private TagsRepository $tagsManager;


    /**
     * TagsController constructor.
     *
     * @param Request $request
     */
    public function __construct(Request $request)
    {
        parent::__construct($request);

        $this->tagsManager = new TagsRepository();
    }


    /**
     * Create new tag
     *
     * @return JsonResponse
     */
    public function create() : JsonResponse
    {
        if($this->request->getMethod() == 'POST')
        {
            if(!empty($this->request->request->get('name')))
            {
                $tags = $this->request->request->get('name');

                if($this->tagsManager->findOne($this->request->request->get('name')))
                {
                    return new JsonResponse(['error' => 'Le tag existe déjà.']);
                }

                $this->tagsManager->create($tags);

                return new JsonResponse($this->tagsManager->findLast());
            }
            else
            {
                return new JsonResponse(['error' => 'Veuillez remplir le formulaire']);
            }
        }
    }
}