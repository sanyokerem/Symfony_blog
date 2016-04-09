<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Symfony\Component\HttpFoundation\Request;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

use AppBundle\Entity\Post;
use AppBundle\Entity\Comment;
use Symfony\Component\Validator\Constraints as Assert;
use AppBundle\Form\Type\PostType;
// use Symfony\Component\HttpFoundation\File\UploadedFile;

class PostController extends Controller
{
    /**
     * @Route("/", name="all_posts")
     * @Template("AppBundle:Post:index.html.twig")
     */
    public function indexAction()
    {  

        $repo = $this->getDoctrine()->getManager()
            ->createQuery("SELECT p AS post, u.name
                FROM AppBundle:Post p 
                LEFT JOIN AppBundle:User u 
                WHERE p.user = u.id ");
            $posts = $repo->getResult();

        return ['posts' => $posts];
    }

    /**
     * @Route("/new", name="new_post")
     * @Template
     */
    public function newAction(Request $r)
    {

        $this->denyAccessUnlessGranted('ROLE_USER');

        $post = new Post;

        $form = $this->createForm(PostType::class, $post);
        // $form->add('saveAndContinue', \Symfony\Component\Form\Extension\Core\Type\SubmitType::class);
        $form->handleRequest($r);
        if ($form->isValid()) {
            $post->setUser($this->getUser());
            if ($post->image){
    
                $filename = $post->image->getClientOriginalName();
                $post->image->move('images', $filename);
                $post->setPath('images' . '/'. $filename);
            }
            $em = $this->getDoctrine()->getManager();
            $em->persist($post);
            $em->flush();

            return $this->redirectToRoute('all_posts');
        }

         return ['form' => $form->createView()];
    }

        /**
     * @Route("/remove/{post}", name = "remove_post")
     * @Template
     */
    public function removeAction(Post $post, Request $r)
    {

        if ($post->getUser() !== $this->getUser()) {
            die("You can't remove another users posts!");
        }

        $em = $this->getDoctrine()->getManager();
        $em->remove($post);
        $em->flush();
        return $this->redirectToRoute('all_posts');
    }

    /**
     * @Route("/update/{post}", name="update_post")
     * @Template("AppBundle:Post:new.html.twig")
     */
    public function updateAction(Request $r, Post $post)
    {
        if ($post->getUser() !== $this->getUser()) {
            die("You can't update another users posts!");
        }

        $form = $this->createForm(PostType::class, $post);
            $form->handleRequest($r);
            if ($form->isValid()) {
                if ($post->image){
    
                    $filename = $post->image->getClientOriginalName();
                    $post->image->move('images', $filename);
                    $post->setPath('images' . '/'. $filename);
                }
            $em = $this->getDoctrine()->getManager();
            $em->flush();
            return $this->redirectToRoute('all_posts');
            }
                        dump($post->image);
        return ['form' => $form->createView()];
    }

    /**
     * @Route("/show/{post}", name = "show_post")
     * @Template
     */
    public function showAction(Post $post, Request $r)
    {
            $em = $this->getDoctrine()->getManager();
            $errors = [];

        if ($r->getMethod() === 'POST') {
            $comment = new Comment;
            $comment
                ->setPost($post)
                ->setAuthor($this->getUser()->getName())
                ->setComment($r->request->get('comment'));
            $em->persist($comment);
            
            $validator = $this->get('validator');
            $errors = $validator
                ->validate($comment);

            if (!count($errors)) {
                $em->flush();
                return $this->redirectToRoute('show_post', ['post' => $post -> getId()]);
            }
        }

        return ['post' => $post, 'errors' => $errors];
    }



    /**
     * @Route("/remove_comment/{comment}", name = "remove_comment")
     * @Template
     */
    public function remove_commentAction(Comment $comment, Request $r)
    {
        $em = $this->getDoctrine()->getManager();
        $em->remove($comment);
        $em->flush();
        return $this->redirectToRoute('all_posts');
    }

}
