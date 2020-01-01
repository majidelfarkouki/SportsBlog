<?php

namespace App\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\ButtonType;
use Symfony\Component\Validator\Constraints\DateTime;

use App\Entity\Posts;

class CRUDController extends Controller
{

    /**
     * @Route("/new")
     */
    public function newAction(Request $request)
    {
        $post = new Posts();

        $form = $this->createFormBuilder($post)
            ->add('title', TextType::class, array('attr' => array('class' => 'form-control col-lg-4 col-md-6 col-sm-4', 'placeholder' => 'Title...')))
            ->add('description', TextareaType::class, array('attr' => array('class' => 'form-control col-lg-10 col-md-10 col-sm-10', 'style' => 'height:150px;', 'placeholder' => 'Post\'s content...')))
            ->add('save', SubmitType::class, array('label' => 'Add new article', 'attr' => array('class' => 'btn btn-primary')))
            ->add('discard', ButtonType::class, array('label' => 'Cancel', 'attr' => array('class' => 'btn btn-outline-danger', 'data-toggle' => 'modal', 'data-target' => '#validationModal')))
            ->getForm();

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid())
        {
            // Extract data from form
            $title = $form['title']->getData();
            $description = $form['description']->getData();

            // Delete JavaScript content
            $description = preg_replace('/<script\b[^>]*>(.*?)<\/script>/is', "", $description);

            // Prepare some fields
            $delimiter = "-";
            $clean = iconv('UTF-8', 'ASCII//TRANSLIT', $title);
            $clean = preg_replace("/[^a-zA-Z0-9\/_|+ -]/", '', $clean);
            $clean = strtolower(trim($clean, '-'));
            $clean = preg_replace("/[\/_|+ -]+/", $delimiter, $clean);
            $url_alias = $clean;
            // $description = nl2br($description);
            $now = new \DateTime('now');

            // Init post object
            $post->setTitle($title);
            $post->setUrlAlias('testnew');
            $post->setDescription($description);
            $post->setPublished('31/12/2019');

            // Save the post in database
            $em = $this->getDoctrine()->getManager();
            $em->persist($post);
            $em->flush();

            // Display success message
            $this->addFlash(
                'notice',
                'Article published'
            );

            // Return to the new post page
            return $this->redirectToRoute('blog_post', array('url_alias' => $url_alias));
            // return $this->redirectToRoute('homepage');
        }

        return $this->render('CRUD/new.html.twig', array(
            'form' => $form->createView(),
        ));
    }

    /**
     * @Route("/edit/{url_alias}")
     */
    public function editAction(Request $request, $url_alias)
    {
        $em = $this->getDoctrine()->getManager();
        $post = $em->getRepository('App:Posts')->findOneByUrl_alias($url_alias);

        $form = $this->createFormBuilder($post)
        ->add('title', TextType::class, array('attr' => array('class' => 'form-control col-lg-4 col-md-6 col-sm-4', 'placeholder' => 'Title...')))
        ->add('description', TextareaType::class, array('attr' => array('class' => 'form-control col-lg-10 col-md-10 col-sm-10', 'style' => 'height:150px;', 'placeholder' => 'Post\'s content...')))
        ->add('save', SubmitType::class, array('label' => 'Save changes', 'attr' => array('class' => 'btn btn-primary')))
        ->add('discard', ButtonType::class, array('label' => 'Cancel', 'attr' => array('class' => 'btn btn-outline-danger', 'data-toggle' => 'modal', 'data-target' => '#validationModal')))
        ->getForm();

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid())
        {
            // Get data from form
            $new_title = $form['title']->getData();
            $new_descritpion = $form['description']->getData();

            // Delete JavaScript content
            $new_descritpion = preg_replace('/<script\b[^>]*>(.*?)<\/script>/is', "", $new_descritpion);

            // Prepare some fields
            $delimiter = "-";
            $clean = iconv('UTF-8', 'ASCII//TRANSLIT', $new_title);
            $clean = preg_replace("/[^a-zA-Z0-9\/_|+ -]/", '', $clean);
            $clean = strtolower(trim($clean, '-'));
            $clean = preg_replace("/[\/_|+ -]+/", $delimiter, $clean);
            $new_url_alias = $clean;
            // $new_descritpion = nl2br($new_descritpion);

            $post->setTitle($new_title);
            $post->setUrlAlias($new_url_alias);
            $post->setDescription($new_descritpion);

            $em->flush();

            return $this->redirectToRoute('blog_post', array('url_alias' => $new_url_alias));
        }

        return $this->render('CRUD/edit.html.twig', array(
            'post' => $post,
            'form' => $form->createView(),
        ));
    }

    /**
     * @Route("/delete/{id_post}")
     */
    public function deleteAction($id_post)
    {
        $em = $this->getDoctrine()->getManager();
        $post = $em->getRepository(Posts::class)->find($id_post);

        $em->remove($post);
        $em->flush();

        return $this->redirectToRoute('all_post', array());
    }

}