<?php

namespace Lpo\CrudBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;
use Lpo\CrudBundle\Task\Exemple\CreateTask;
use Lpo\CrudBundle\Task\Exemple\UpdateTask;

class ExempleController extends Controller
{

    /**
     * @Template("LpoCrudBundle:Exemple:index.html.twig")
     */
    public function indexAction($name)
    {
        return [];
    }

    /**
     * @Template("LpoCrudBundle:Exemple:create.html.twig")
     */
    public function createAction(Request $request)
    {
        $task = new CreateTask();
        $form = $this->createForm('exemple', $task);

        $form->handleRequest($request);

        if ($form->isValid()) {
            $orderer = $this->get('lpo_crud.orderer');
            $orderer->order($task);
            // Message
            $this->get('session')->getFlashBag()->add(
                'success',
                $this->get('translator')->trans('admin.exemple.create.success')
            );

            return $this->redirect($this->generateUrl('lpo_crud_create_exemple'));
        }

        $formView = $form->createView();

        return ['form' => $formView];
    }

    /**
     * @Template("LpoCrudBundle:Exemple:edit.html.twig")
     */
    public function editAction($id, Request $request)
    {
        $task = new UpdateTask();
        $form = $this->createForm('exemple', $task);

        $form->handleRequest($request);

        if ($form->isValid()) { // If form is post
            $orderer = $this->get('lpo_crud.orderer');
            $orderer->order($task);
            // Message
            $this->get('session')->getFlashBag()->add(
                'success',
                $this->get('translator')->trans('admin.exemple.edit.success')
            );

            return $this->redirect($this->generateUrl('lpo_crud_create_exemple'));
        }

        //If form is initialize
        $exempleRepo = $this->get('lpo_infra.manager.default')->getRepository('Lpo\InfraBundle\Entity\Exemple');
        $exemple     = $exempleRepo->find($id);
        $task        = new UpdateTask([
            'id' => $id,
            'nom' => $exemple->getNom(),
            'description' => $exemple->getDescription()
        ]);
        $form        = $this->createForm('exemple', $task);
        $formView    = $form->createView();

        return ['form' => $formView];
    }

    public function removeAction($name)
    {

    }
}
