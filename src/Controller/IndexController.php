<?php

namespace App\Controller;

use App\Entity\Article;
use App\Entity\Category;
use App\Form\ArticleType;
use App\Form\CategoryType;
use Doctrine\Persistence\ManagerRegistry as PersistenceManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Request;  
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\Form\Extension\Core\Type\TextType; 
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\RedirectResponse;

class IndexController extends AbstractController
{
  //List des articles
    #[Route('/', name: 'article_list', methods:['GET'])]
    public function home(PersistenceManagerRegistry $managerRegistry)  {
        $articles = $managerRegistry->getRepository(Article::class)->findAll();
        return $this->render('articles/index.html.twig',['articles' => $articles]); 

      
    }

    //ajouter un article
    #[Route('/new', name: 'new_article', methods:['GET','POST'])]
    public function new(PersistenceManagerRegistry $managerRegistry,Request $request)  {
      $article = new Article();
      $form = $this->createForm(ArticleType::class,$article);
      $form->handleRequest($request);
      if($form->isSubmitted() && $form->isValid()) 
      { 
        $article = $form->getData();
        $entityManager =$managerRegistry->getManager();
        $entityManager->persist($article);
        $entityManager->flush();
        return $this->redirectToRoute('article_list');
    }
    return $this->render('articles/new.html.twig',['form' => $form->createView()]);
  }


  //Details d'un article
  #[Route('/article/{id}', name:"article_show")]
  public function show(PersistenceManagerRegistry $managerRegistry,$id)  {
    $article=$managerRegistry->getRepository(Article::class)->find($id);
    return $this->render('articles/show.html.twig', array('article' => $article)); 
  }

  //Modifier un article
  #[Route('/article/edit/{id}',name:"edit_article",methods:['GET','POST'])]
  public function edit(PersistenceManagerRegistry $managerRegistry,Request $request,$id)  {
    $article = new Article();
    $article=$managerRegistry->getRepository(Article::class)->find($id);
    $form = $this->createForm(ArticleType::class,$article);
    $form->handleRequest($request);
    if($form->isSubmitted() && $form->isValid()) 
    { 
      $entityManager = $managerRegistry->getManager(); 
      $entityManager->flush(); 
      return $this->redirectToRoute('article_list');
  }
  return $this->render('articles/edit.html.twig', ['form' => $form->createView()]);
}


//supprimer un article 
#[Route('/article/delete/{id}',name:"delete_article")]
public function delete(PersistenceManagerRegistry $managerRegistry,Request $request,$id):RedirectResponse  {
  $article=$managerRegistry->getRepository(Article::class)->find($id);
  $entityManager = $managerRegistry->getManager(); 
  $entityManager->remove($article);
  $entityManager->flush();
  $this->addFlash(type:'success',message:'L article est supprimé');
  $response = new Response();
  $response->send();
  return $this->redirectToRoute('article_list');
}

//Ajouter une nouvelle category
#[Route('/category/newCat', name: 'new_category', methods:['GET','POST'])]
public function newCategory(PersistenceManagerRegistry $managerRegistry,Request $request) {
  $category = new Category();
  $form = $this->createForm(CategoryType::class,$category);
  $form->handleRequest($request);
  if($form->isSubmitted() && $form->isValid()) {
    $category = $form->getData();
    $entityManager=$managerRegistry->getManager();
    $entityManager->persist($category);
    $entityManager->flush();

  }
  return $this->render('categ/newCategory.html.twig',['form'=> $form->createView()]);
}


}

