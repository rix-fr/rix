---
type:               "post"
title:              "Ansible - Roles et collections, savoir factoriser."
date:               "2024-02-26"
lastModified:       ~
tableOfContent:     true
description:        "Automatiser c'est bien, faire en sorte que cela soit réutilisable simplement c'est mieux. Mise en oeuvre des collections et roles avec Ansible."
thumbnail:          "content/images/blog/thumbnails/ansible-les-roles.jpg"
tags:               ["cours", "ansible", "automation", "playbook", "collection", "role"]
categories:         ["cours"]
authors:            ["gfaivre"]
---

## Préambule

Ce cours est utilisé dans le cadre de TP au sein de l'IUT Lyon 1. Il est notamment dispensé à des étudiants peu ou pas familiers avec les stratégies d'automatisation et de déploiement des infrastructures.
Bien que très axé débutants il peut également représenté une possibilité de monter « rapidement » pour certaines équipes sur les principes fondamentaux d'Ansible afin de disposer du bagage minimal nécessaire à son utilisation.

Il s'agit bien évidemment de supports à vocation pédagogique qui **ne sont pas toujours transposables** à une activité professionnelle.

## Pré-requis

Avoir suivi/lu les cours concernant: 

- les [inventaires](/blog/cours/ansible/ansible-les-inventaires-statiques);
- les [playbooks](/blog/cours/ansible/ansible-les-playbooks);
- les [variables](/blog/cours/ansible/ansible-les-variables).

## Introduction

Précédemment nous avons vu l'utilisation des concepts fondamentaux d'Ansible, toutefois au fur et à mesure de l'évolution de ce TD vous avez du vous poser plusieurs questions et notamment celles de la **maintenance et de la réutilisation** de tout ce qu'on à fait.
Et vous avez bien raison, car jusqu'à présent nous n'avons rien produit de bien réutilisable et de correctement maintenable.

En effet nos playbooks contiennent de nombreuses instructions qui bien que liées n'ont pas forcément pour finalité de cohabiter et l'ensemble de ces instructions paraissent en l'état, bien difficilement transposables à d'autres projets/contextes.

Il est donc plus que temps d'intégrer le concept de **rôle** et plus largement de **collection**.

## Les rôles

Au sens Ansible un rôle est assimilable à un **regroupement de tâches** qui (normalement) s'orientent vers un même objectif. « Normalement » car encore une fois c'est vous qui êtes maître de ce que vous embarquer dans un rôle mais d'expérience je ne saurai que vous conseiller de bien définir le périmètre d'application de chacun de vos rôles et de vous y tenir ;) ! 

On peut ainsi s'imaginer un rôle dédié à la gestion de notre serveur web nginx ou encore à un serveur de bases de données PostgreSQL.

La structure d'un rôle est très similaire à l'organisation que l'on a pu voir jusqu'à présent, on retrouvera ainsi une arborescence type:

```yaml

roles/
    nginx/                # this hierarchy represents a "role"
        tasks/            #
            main.yml      #  <-- tasks file can include smaller files if warranted
        handlers/         #
            main.yml      #  <-- handlers file
        templates/        #  <-- files for use with the template resource
            app.conf.j2   #  <------- templates end in .j2
        files/            #
            bar.conf      #  <-- files for use with the copy resource
            foo.sh        #  <-- script files for use with the script resource
        vars/             #
            main.yml      #  <-- variables associated with this role
        defaults/         #
            main.yml      #  <-- default lower priority variables for this role
        meta/             #
            main.yml      #  <-- role dependencies
        library/          # roles can also include custom modules
        module_utils/     # roles can also include custom module_utils
        lookup_plugins/   # or other types of plugins, like lookup in this case
```

Ansible prévoit un répertoire dédié aux rôles dans lequel nous retrouverons **un répertoire par rôle**, chacun de ces rôles obéissant à la structure définie ci-dessus.

!!! info "Structure d'un rôle"
    Un rôle doit contenir **au moins** un des répertoires de l'arborescence ci-dessus. À l'inverse si certains répertoires ne sont pas nécessaires à son fonctionnement, ils peuvent être omis.
    Par défaut Ansible « recherchera » les instructions d'un répertoire dans un fichier `main.yml`.

Ansible par défaut « recherchera » des rôles à différents endroits:

- Dans l'emplacement destiné aux **collections** (dont nous parlerons juste après);
- Dans un répertoire `roles` relativement à la position de notre fichier de playbook;
- À partir de la clé `role_path` configurable notamment dans le fichier de configuration `ansible.cfg`;
- Et pour finir dans le répertoire ou se trouve le playbook.

Il est possible de faire cohabiter tout ce beau monde et d'avoir par exemple des rôles communautaires tout en ayant des rôles propres à votre projet / société mais qui n'ont pas forcément vocation à être partagés !

### Créer son premier rôle

Reprenons nos travaux précédent. Nous avions au final plusieurs tâches, chacune chargée de faire des choses bien différentes (Nginx, PHP, motd...)
Notre objectif pour cette fois sera de **refactoriser ces sections** de manière à les transformer en **rôles**.

#### Un rôle Motd

Rien de bien compliqué pour commencer, histoire de ne pas vous perdre tout de suite ;)
Créons notre arborescence dans un répertoire `roles` **à la racine de notre répertoire de travail** en y ajoutant un répertoire `motd`.
Dans ce dernier nous allons ensuite créer les répertoires `tasks` et `defaults`.

À l'intérieur de chacun de ces répertoires nouvellement créés nous ajouterons un fichier `main.yml`, pour l'instant vide.

Nous créerons également une arborescence `templates/scripts` dans notre role et nous y « rapatrierons » nos fichiers de template `production.j2` et `staging.j2` du répertoire `templates` à la racine de notre projet vers le répertoire `templates/scripts` de notre rôle.

Vous devriez à ce stade disposer d'une arborescence pour le rôle **motd** similaire à la suivante:

```shell
motd
├── defaults
│   └── main.yml
├── tasks
│   ├── main.yml
│   └── scripts.yml
└── templates
    └── scripts
        ├── production.j2
        └── staging.j2
```

À présent nous allons « redescendre » la tâche motd de notre playbook `webservers.yml` vers le fichier `tasks/main.yml` de notre rôle.

Il nous faudra adapter notre tâche à notre nouvelle arborescence:

```yaml
- name: MOTD > Installing template
  ansible.builtin.template:
    src: "scripts/{{stage}}.j2"
    dest: "/etc/update-motd.d/10-welcome"
    owner: root
    group: root
    mode: "0755"
```

À ce stade vous avez un premier rôle très simple et fonctionnel, il nous reste à assurer son déclenchement au niveau de notre playbook `webservers.yml` que nous allons modifier de manière à intégrer l'exécution de notre rôle:

```yaml
- name: "Configuring webservers"
  hosts: webservers

  roles:
    - role: motd

  pre_tasks:
    - name: "Common tasks"
    ...
```

En exécutant la commande `ansible-playbook -i inventories main.yml` vous ne devriez constater aucun changements notables si ce n'est que l'exécution des instructions relatives à la configuration de motd se fait désormais à partir de notre nouveau rôle.

### Organisation et structures de contrôle avancées

Dans le but d'étoffer et de rendre **un peu plus robuste** notre rôle nous allons introduire quelques contraintes:

- La possibilité d'activer ou non une fonctionnalité de contrôle des motd existants côté serveur (tolère t-on ou non la présence de motd non gérés par notre rôle ?);
- La possibilité de choisir le répertoire d'installation de nos scripts motd;
- La possibilité d'avoir plusieurs scripts déployés (actuellement nous n'en déployons qu'un seul).

Mais avant de commencer nous allons, pour l'exemple voir que nous pouvons tout à fait (et c'est d'ailleurs très courant) géré l'inclusion de tâches dans nos rôles comme dans nos playbook principaux.

Créons dans le répertoire `roles/motd/tasks` un fichier nommé `scripts.yml` dans lequel nous allons transférer le contenu du fichier `roles/motd/tasks/main.yml`.

À présent dans notre fichier `roles/motd/tasks/main.yml` nous ajoutons les instructions suivantes:

```yaml
---

- name: Scripts
  ansible.builtin.import_tasks: scripts.yml
  tags:
    - workshop_motd
    - workshop_motd.scripts
```

On y retrouve les instructions d'import de tâches mais également les **tags spécifiques** qui nous permettrons de [cibler nos exécutions](/blog/cours/ansible/ansible-les-playbooks#taguer-ses-taches).

### Déclarer les variables de rôles

Souvenez-vous, nous avons créé un répertoire `defaults`, celui-ci va contenir les variables utilisées par notre rôle et leur valeur par défaut.

#### Initialisation de variables par « défaut »

Une bonne pratique consiste à toujours déclarer les variables qui seront utilisées dans notre rôle.
Dans notre cas nous utiliserons 3 variables que nous allons donc déclarer dans le fichier `roles/motd/defaults/main.yml`.

Dans le fichier `defaults/main.yml` nous ajouterons donc:

```yaml
---

workshop_motd_scripts_exclusive: true
workshop_motd_scripts_dir: /etc/update-motd.d
workshop_motd_scripts: []
```

**Dans l'ordre:**

- Une variable qui nous servira à **configurer l'exclusivité** des scripts motd;
- Une seconde qui permettra de configurer le répertoire cible de destination de nos scripts;
- Et enfin une dernière qui nous permettra de paramétrer les scripts à déployer côté serveur.

#### Exclusivité

Première contrainte, faire en sorte que notre rôle **soit la référence** en terme d'état de la machine cible.
Pour se faire, l'idée et de ne conserver à l'exécution que les scripts **déclarés** dans notre configuration.

Avant de nous lancer, il va falloir faire évoluer légèrement notre variable `workshop_motd_scripts` que vous retrouverez dans les fichiers:

- `group_vars/production.yml` que nous modifierons comme ci-dessous:

```yaml
workshop_motd_scripts:
  - file: 10-message
    template: scripts/{{ stage }}.j2
    message: Attention environnement de production !
  - file: 20-uname
```

- `group_vars/staging.yml` que nous modifierons comme ci-dessous:

```yaml
workshop_motd_scripts:
  - file: 10-message
    template: scripts/{{ stage }}.j2
    message: Environnement de staging !
```

Nous avons fait évoluer la structure de notre variable vers une structure plus complexe:
- `file`: contient le nom du fichier attendu côté machine cible;
- `template`: indique le fichier de template à utiliser;
- `message`: contient le message à afficher dans notre motd.

Nous avons donc défini **deux scripts motd** à installer sur notre environnement de production et **un seul** sur notre environnement de staging.

!!! info "L'option register"
    L'utilisation de l'option `register` permet de créer une variable à partir des données de sortie résultantes de l'exécution d'une tâche, dans le but de réutiliser cette variable **dans une autre tâche à venir**.

Nous pouvons donc à présent positionner en tout début du fichier `roles/motd/tasks/scripts.yaml` le bloc d'instruction suivant:

```yaml
---

- name: Checking files to exclude
  ansible.builtin.set_fact:
    workshop_scripts_list: "{{ workshop_scripts_list|default([]) + [item.file] }}"
  changed_when: false
  loop: "{{ workshop_motd_scripts }}"

- name: MOTD > Exclusive
  ansible.builtin.find:
    path: "{{ workshop_motd_scripts_dir }}"
    file_type: file
    patterns: "*"
    excludes: "{{ workshop_scripts_list }}"
  changed_when: false
  register: __workshop_motd_scripts_exclusive_find
  when: workshop_motd_scripts_exclusive
```

Le premier bloc d'instruction aura vocation à lister sous forme d'un tableau « plat » la liste des scripts que nous avons décidé d'avoir sur notre serveur (variable `workshop_motd_scripts`).
Le but étant de récupérer une variable contenant des informations compréhensibles par l'option `excludes` du module `ansible.builtin.find` dont le comportement est similaire à la commande UNIX.

La seconde tâche aura pour but de lister les fichiers côté serveur correspondant aux options que l'on aura passées au module à savoir:

- N'importe quel fichier de type fichier (Il n'y a pas de redondance ici, n'oubliez pas que dans un système UNIX, TOUT est fichier)
- Dont le nom remplit les conditions du paramètre `pattern` à savoir « * » soit n'importe quel caractère ou pour résumer tous les fichiers ;);
- En excluant les fichiers présents dans la variable `workshop_scripts_list`.

!!! info "L'instruction loop"
    L'instruction « loop » permet d'adopter un comportement itératif au niveau de nos tâches. Ainsi la variable considérée en paramètre de l'instruction sera itérée dans la tâche concernée. Chaque itération rendant accessible les éléments via la variable « item ».

**Les points à retenir:**
- La tâche chargée de lister les scripts prévus dans nos fichiers de « provisionning »;
- L'utilisation du module `file` et de ses paramètres qui nous permet de récupérer tous les éléments (*) de type fichier du répertoire cible;
- L'utilisation de notre variable de rôle `workshop_motd_scripts_dir`;
- Le conditionnement de l'exécution avec l'instruction `when` en fonction de la valeur de la variable `workshop_motd_scripts_exclusive`.

!!! info "L'option changed_when"
    L'option `changed_when` nous permet de « décider » si une tâche a modifié (au sens Ansible) notre machine cible. En fonction de son retour nous pouvons donc décider si un changement doit être mentionné ou si un `handler` doit être déclenché. Bon à savoir également, si vous y définissez plusieurs conditions celles-ci sont jointes avec un opérateur `and` par défaut.

Nous avons donc récupéré la liste des fichiers présents sur notre machine cible il nous reste à présent à supprimer ceux qui ne sont pas gérés par notre script.

```yaml

- name: Remove file (delete file)
  ansible.builtin.file:
    path: "{{ item.path }}"
    state: absent
  loop: "{{ __workshop_motd_scripts_exclusive_find.files }}"
```

On retrouve à cette occasion l'utilisation du mot clé «**loop**» afin d'itérer sur la liste des fichiers à supprimer.

## Point de progression

Nous avons à travers cette série d'articles, fait le tour des principes fondamentaux d'Ansible. Ceci n'est bien évidemment que le début du chemin vers la maîtrise d'Ansible mais c'est une première étape fondamentale avant d'aller plus loin ! 

## Aller plus loin avec les sources

- https://docs.ansible.com/ansible/latest/playbook_guide/playbooks_reuse_roles.html
- https://docs.ansible.com/ansible/latest/playbook_guide/playbooks_reuse_roles.html#embedding-modules-and-plugins-in-roles
- https://docs.ansible.com/ansible/latest/playbook_guide/playbooks_error_handling.html#defining-changed
- https://docs.ansible.com/ansible/latest/playbook_guide/playbooks_variables.html#registering-variables
https://docs.ansible.com/ansible/latest/playbook_guide/playbooks_loops.html

