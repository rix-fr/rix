---
type:               "post"
title:              "Faire fonctionner des conteneurs Docker dans WSL."
date:               "2023-04-11"
lastModified:       ~
tableOfContent:     true
description:        "Avec l'intégration WSL2 de Windows et Docker Desktop, comment utiliser des conteneurs Docker dans une machine virtuelle WSL ?"

thumbnail:          "content/images/blog/thumbnails/nature.jpg"
credits:            { name: "Jason Cooper", url: "https://unsplash.com/@salty_sandals" }
tags:               ["cours", "windows", "docker", "conteneur"]
categories:         ["cours"]
authors:            ["gfaivre"]
---

Avec l'arrivée de Docker Desktop il est dorénavant aisé de faire « tourner » des conteneurs Docker sous Windows.
Il est par contre moins simple d'utiliser d'autres outils propres au monde UNIX que nous aurons besoin d'utiliser avec le Lazy Ansible de Manala (make par exemple).

Et comme nous préférons éviter d'installer trop de choses sur les machines hôtes nous opterons pour un fonctionnement qui reste relativement élégant à savoir lancer une machine WSL Debian et y faire tourner notre conteneur Docker.
De cette manière nous disposerons de l'ensemble de l'outillage Linux, sans avoir à l'installer sur notre poste.

## Pré-requis

- Windows 10 au minimum
- Installer terminal windows (pour le confort).

## Installer WSL 2 et lancer une machine virtuelle

- S'assurer que l'on utilise bien la version 2 de WSL: `wsl --set-default-version 2`
- Installer et lancer une machine virtuelle Debian: `wsl --install -d Debian`

Il faudra ensuite renseigner un nom d'utilisateur ainsi qu'un mot de passe (à ne pas perdre de préference).
Vous devriez au final obtenir un shell comme ci-dessous, félicitation vous êtes dans une machine virtuelle Debian WSL !

<figure>
    <img src="/content/images/blog/2023/cours-docker-wsl/wsl_debian_shell.jpg" alt="Un shell WSL">
    <figcaption>
      <span class="figure__legend">Un shell WSL</span>
    </figcaption>
</figure>

## Installer Docker Desktop

La partie la plus simple, téléchargez et installez le ici: [https://docs.docker.com/desktop/windows/wsl/#download](https://docs.docker.com/desktop/windows/wsl/#download)

### Configuration

Il y a quelques options à vérifier / activer pour un bon fonctionnement.

- Tout d'abord vérifier que le support de WSL 2 est activé dans les paramètres (Resources -> WSL integration);

<figure>
    <img src="/content/images/blog/2023/cours-docker-wsl/docker-desktop-settings.jpg" alt="Les paramètres Docker Desktop">
    <figcaption>
      <span class="figure__legend">Les paramètres Docker Desktop</span>
    </figcaption>
</figure>

- Ensuite vérifier que le support pour votre distribution est activé (Debian);

## Tester le fonctionnement de Docker dans une machine virtuelle WSL

Il faudra pour cela quitter et relancer (Dans un PowerShell `wsl -d Debian`) votre machine virtuelle Debian.
Une fois à l'intérieur de celle-ci il sera nécessaire de donner les droits à votre utilisateur d'utiliser Docker en l'ajoutant au groupe du même nom.

```
usermod -a -G docker <username>
```

Pour terminer la commande `docker ps` devrait vous renvoyer l'écran ci-dessous:

<figure>
    <img src="/content/images/blog/2023/cours-docker-wsl/add-user-to-docker-group.jpg" alt="Ajouter un utilisateur au groupe docker">
    <figcaption>
      <span class="figure__legend">Ajouter un utilisateur au groupe docker</span>
    </figcaption>
</figure>

## Aller plus loin avec les sources

- https://learn.microsoft.com/fr-fr/windows/wsl/basic-commands
- https://learn.microsoft.com/fr-fr/training/modules/wsl/wsl-introduction/
- https://learn.microsoft.com/fr-fr/windows/wsl/install
- https://learn.microsoft.com/fr-fr/windows/wsl/

