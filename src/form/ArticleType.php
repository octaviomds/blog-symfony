// src/Form/ArticleType.php
namespace App\Form;

use App\Entity\Article;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ArticleType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('titre', TextType::class, [
                'label' => 'Titre de l\'article',
                'attr' => ['class' => 'form-control', 'placeholder' => 'Entrez le titre']
            ])
            ->add('contenu', TextareaType::class, [
                'label' => 'Contenu',
                'attr' => ['class' => 'form-control', 'rows' => 10, 'placeholder' => 'Écrivez votre article...']
            ])
            ->add('auteur', TextType::class, [
                'label' => 'Auteur',
                'attr' => ['class' => 'form-control', 'placeholder' => 'Votre nom']
            ])
            ->add('enregistrer', SubmitType::class, [
                'label' => 'Publier l\'article',
                'attr' => ['class' => 'btn btn-primary mt-3']
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Article::class,
        ]);
    }
}

// src/Controller/BlogController.php
namespace App\Controller;

use App\Entity\Article;
use App\Form\ArticleType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class BlogController extends AbstractController
{
    #[Route('/', name: 'blog_index')]
    public function index(EntityManagerInterface $em): Response
    {
        $articles = $em->getRepository(Article::class)
            ->findBy([], ['datePublication' => 'DESC']);

        return $this->render('blog/index.html.twig', [
            'articles' => $articles
        ]);
    }

    #[Route('/article/{id}', name: 'blog_show')]
    public function show(Article $article): Response
    {
        return $this->render('blog/show.html.twig', [
            'article' => $article
        ]);
    }

    #[Route('/nouveau', name: 'blog_new')]
    public function new(Request $request, EntityManagerInterface $em): Response
    {
        $article = new Article();
        $form = $this->createForm(ArticleType::class, $article);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($article);
            $em->flush();

            $this->addFlash('success', 'Article publié avec succès !');
            return $this->redirectToRoute('blog_show', ['id' => $article->getId()]);
        }

        return $this->render('blog/new.html.twig', [
            'form' => $form->createView()
        ]);
    }

    #[Route('/admin', name: 'blog_admin')]
    public function admin(EntityManagerInterface $em): Response
    {
        $articles = $em->getRepository(Article::class)
            ->findBy([], ['datePublication' => 'DESC']);

        return $this->render('blog/admin.html.twig', [
            'articles' => $articles
        ]);
    }

    #[Route('/supprimer/{id}', name: 'blog_delete', methods: ['POST'])]
    public function delete(Article $article, EntityManagerInterface $em): Response
    {
        $em->remove($article);
        $em->flush();

        $this->addFlash('success', 'Article supprimé avec succès !');
        return $this->redirectToRoute('blog_admin');
    }
}
