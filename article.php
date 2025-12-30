<?php
// src/Entity/Article.php
namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity]
#[ORM\Table(name: 'articles')]
class Article
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\Column(type: 'string', length: 255)]
    #[Assert\NotBlank(message: 'Le titre est obligatoire')]
    #[Assert\Length(min: 3, max: 255)]
    private ?string $titre = null;

    #[ORM\Column(type: 'text')]
    #[Assert\NotBlank(message: 'Le contenu est obligatoire')]
    private ?string $contenu = null;

    #[ORM\Column(type: 'string', length: 100)]
    #[Assert\NotBlank]
    private ?string $auteur = null;

    #[ORM\Column(type: 'datetime')]
    private ?\DateTimeInterface $datePublication = null;

    public function __construct()
    {
        $this->datePublication = new \DateTime();
    }

    public function getId(): ?int { return $this->id; }
    public function getTitre(): ?string { return $this->titre; }
    public function setTitre(string $titre): self { $this->titre = $titre; return $this; }
    public function getContenu(): ?string { return $this->contenu; }
    public function setContenu(string $contenu): self { $this->contenu = $contenu; return $this; }
    public function getAuteur(): ?string { return $this->auteur; }
    public function setAuteur(string $auteur): self { $this->auteur = $auteur; return $this; }
    public function getDatePublication(): ?\DateTimeInterface { return $this->datePublication; }
    public function setDatePublication(\DateTimeInterface $date): self { $this->datePublication = $date; return $this; }
}

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

// templates/base.html.twig
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{% block title %}Mon Blog Symfony{% endblock %}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { padding-top: 60px; }
        .article-card { margin-bottom: 20px; transition: transform 0.2s; }
        .article-card:hover { transform: translateY(-5px); box-shadow: 0 4px 15px rgba(0,0,0,0.1); }
    </style>
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-dark bg-primary fixed-top">
    <div class="container">
        <a class="navbar-brand" href="{{ path('blog_index') }}">📝 Mon Blog</a>
        <div class="navbar-nav ms-auto">
            <a class="nav-link" href="{{ path('blog_index') }}">Accueil</a>
            <a class="nav-link" href="{{ path('blog_new') }}">Nouvel Article</a>
            <a class="nav-link" href="{{ path('blog_admin') }}">Admin</a>
        </div>
    </div>
</nav>

<div class="container">
    {% for message in app.flashes('success') %}
    <div class="alert alert-success alert-dismissible fade show mt-3">
        {{ message }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    {% endfor %}

    {% block body %}{% endblock %}
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

<?php
// templates/blog/index.html.twig
?>
{% extends 'base.html.twig' %}

{% block title %}Accueil - Blog{% endblock %}

{% block body %}
<div class="row mt-4">
    <div class="col-12">
        <h1 class="mb-4">Articles du Blog</h1>

        {% if articles is empty %}
        <div class="alert alert-info">
            Aucun article pour le moment. <a href="{{ path('blog_new') }}">Créez le premier !</a>
        </div>
        {% else %}
        {% for article in articles %}
        <div class="card article-card">
            <div class="card-body">
                <h2 class="card-title">
                    <a href="{{ path('blog_show', {id: article.id}) }}" class="text-decoration-none">
                        {{ article.titre }}
                    </a>
                </h2>
                <p class="text-muted">
                    Par {{ article.auteur }} - {{ article.datePublication|date('d/m/Y à H:i') }}
                </p>
                <p class="card-text">{{ article.contenu|slice(0, 200) }}...</p>
                <a href="{{ path('blog_show', {id: article.id}) }}" class="btn btn-primary">Lire la suite</a>
            </div>
        </div>
        {% endfor %}
        {% endif %}
    </div>
</div>
{% endblock %}

<?php
// templates/blog/show.html.twig
?>
{% extends 'base.html.twig' %}

{% block title %}{{ article.titre }}{% endblock %}

{% block body %}
<div class="row mt-4">
    <div class="col-lg-8 mx-auto">
        <article class="card">
            <div class="card-body">
                <h1 class="card-title">{{ article.titre }}</h1>
                <p class="text-muted mb-4">
                    Par {{ article.auteur }} - {{ article.datePublication|date('d/m/Y à H:i') }}
                </p>
                <div class="article-content">
                    {{ article.contenu|nl2br }}
                </div>
                <hr class="my-4">
                <a href="{{ path('blog_index') }}" class="btn btn-secondary">← Retour aux articles</a>
            </div>
        </article>
    </div>
</div>
{% endblock %}

<?php
// templates/blog/new.html.twig
?>
{% extends 'base.html.twig' %}

{% block title %}Nouvel Article{% endblock %}

{% block body %}
<div class="row mt-4">
    <div class="col-lg-8 mx-auto">
        <h1 class="mb-4">Créer un Nouvel Article</h1>

        <div class="card">
            <div class="card-body">
                {{ form_start(form) }}
                {{ form_row(form.titre) }}
                {{ form_row(form.auteur) }}
                {{ form_row(form.contenu) }}
                {{ form_row(form.enregistrer) }}
                {{ form_end(form) }}
            </div>
        </div>
    </div>
</div>
{% endblock %}

<?php
// templates/blog/admin.html.twig
?>
{% extends 'base.html.twig' %}

{% block title %}Administration{% endblock %}

{% block body %}
<div class="row mt-4">
    <div class="col-12">
        <h1 class="mb-4">Panneau d'Administration</h1>

        <table class="table table-striped">
            <thead class="table-dark">
            <tr>
                <th>ID</th>
                <th>Titre</th>
                <th>Auteur</th>
                <th>Date</th>
                <th>Actions</th>
            </tr>
            </thead>
            <tbody>
            {% for article in articles %}
            <tr>
                <td>{{ article.id }}</td>
                <td>{{ article.titre }}</td>
                <td>{{ article.auteur }}</td>
                <td>{{ article.datePublication|date('d/m/Y H:i') }}</td>
                <td>
                    <a href="{{ path('blog_show', {id: article.id}) }}" class="btn btn-sm btn-info">Voir</a>
                    <form method="post" action="{{ path('blog_delete', {id: article.id}) }}" style="display:inline;"
                          onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer cet article ?');">
                        <button type="submit" class="btn btn-sm btn-danger">Supprimer</button>
                    </form>
                </td>
            </tr>
            {% endfor %}
            </tbody>
        </table>
    </div>
</div>
{% endblock %}
