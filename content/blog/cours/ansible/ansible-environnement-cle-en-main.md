---
type:               "post"
title:              "Ansible - Un environnement de travail clé en main avec Lazy Ansible."
date:               "2023-11-22"
lastModified:       ~
tableOfContent:     true
description:        "Utilisation de la recipe Lazy Ansible du projet Manala pour mettre en oeuvre un environnement de travail dédié Ansible."

thumbnail:          "content/images/blog/thumbnails/lazy-ansible.jpg"
tags:               ["cours", "ansible", "automation", "manala"]
categories:         ["cours", "ansible"]
authors:            ["gfaivre"]
---

## Préambule

Les environnements dits « lazy » issus du projet [Manala](https://github.com/manala/manala-recipes) sont des outils destinés à mettre en oeuvre de manière rapide des environnements de travail.

Leur finalité étant **multiple**:

- Être en capacité de déployer un environnement sans être familier avec l'outil cible;
- Ne pas avoir à installer et/ou modifier sa configuration locale (sur la machine de travail);
- Disposer d'environnements homogènes de manière à favoriser le collaboratif.

Dans le cadre de travaux autour d'Ansible ou si vous suivez la partie « cours » nous utiliserons la « recipe » qui lui est dédiée (https://github.com/manala/manala-recipes/tree/master/lazy.ansible), son utilisation nécessite l'installation de [Manala](https://manala.github.io/manala/installation/).

## Pré-requis

- Un environnement [Docker / Docker compose](https://docs.docker.com/engine/install/) fonctionnel
- Manala installé

## Mise en route 

La mise en place d'un nouvel environnement en utilisant Manala est relativement simple, il nous suffit de l'initialiser dans un répertoire dédié (cela peut-être un projet existant) à l'aide de la commande `manala init`. 

Démonstration ci-dessous:

<figure>
    <img src="content/images/blog/2023/ansible/lazy-ansible/manala_init.gif">
    <figcaption>
      <span class="figure__legend">Création d'un environnement Ansible avec Manala.</span>
    </figcaption>
</figure>

Nous disposons ainsi d'un environnement Ansible « **conteneurisé** » utilisable en quelques secondes sans n'avoir **rien à installer sur nos postes** (à l'exception de docker bien évidemment).
Et pour ceux et celles qui doivent faire avec plusieurs versions d'Ansible dans leur quotidien, cela permet d'avoir des environnements **isolés et dédiés** à certaines versions de l'outils.

## Fichiers de configuration

Il est bien évidemment possible à partir des fichiers de configuration Manala, d'agir sur les configurations d'ansible mais également [la configuration SSH](/blog/cours/utiliser-la-configuration-ssh-client).

Pour cela il faudra modifier le fichier `.manala.yaml` qui doit, après la manipulation précédente, se trouver à la racine de votre répertoire de travail.

!!! success "Prendre en compte vos modifications"
    Si vous modifiez les fichiers de configuration comme indiqué ci-dessous il faudra penser à utiliser la commande `manala up` afin que vos modifications soient bien prises en compte.

### Configurer Ansible 

Il est possible d'interagir sur la configuration Ansible à partir de la section suivante: 

```yaml
system:
    ansible:
        version: 2.15.5
        config: |
            [defaults]
            control_path_dir = /tmp/ansible/cp
            [privilege_escalation]
            become = True
            become_flags = -H -S
            [ssh_connection]
            control_path = /tmp/%%h-%%r
```

On notera qu'il est possible d'agir sur la version d'ansible utilisée dans notre conteneur Docker mais également sur les directives de configuration propres à Ansible (https://docs.ansible.com/ansible/latest/reference_appendices/config.html).

!!! info "Le fichier ansible.cfg"
    Les modifications de configuration comme ci-dessus se traduisent par l'ajout de directives dans le fichier `/etc/ansible/ansible.cfg`. Il est possible de surcharger ce fichier en placant un fichier du même nom à la racine des répertoires de travail de vos projets permettant ainsi l'introduction de directives spécifiques à chacun d'entre eux.

### Configurer SSH

Concernant SSH le fonctionnement est le même, on retrouve une section dédiée au sein du fichier `.manala.yaml` qui nous permettra de jouer sur les directives de configuration SSH:

```yaml
ssh:
    config: |
        Host *
            User debian
            ForwardAgent yes
```

Et vous voilà en quelques lignes en capacité d'utiliser un environnement Ansible.

### Configurer GIT

Toujours dans le même fichier, la section cette fois-ci sera la suivante:

```yaml
git:
    config: |
        # Silence false positive dubious ownership errors
        #[safe]
        #directory = *
```

Vous voilà prêt à attaquer [Ansible](/blog/cours/ansible/ansible-premiers-pas) ;)
