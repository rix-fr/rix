---
type:               "post"
title:              "Ansible - Les variables"
date:               "2024-01-29"
lastModified:       ~
tableOfContent:     true
description:        "Les variables avec Ansible, introduisons du dynamisme dans nos playbooks !"
thumbnail:          "content/images/blog/thumbnails/ansible-les-variables.jpg"
tags:               ["cours", "ansible", "automation", "variables"]
categories:         ["cours"]
authors:            ["gfaivre"]
---

## Préambule

Ce cours est utilisé dans le cadre de TP au sein de l'IUT Lyon 1. Il est notamment dispensé à des étudiants peu ou pas familiers avec les stratégies d'automatisation et de déploiement des infrastructures.
Bien que très axé débutants il peut également représenté une possibilité de monter « rapidement » pour certaines équipes sur les principes fondamentaux d'Ansible afin de disposer du bagage minimal nécessaire à son utilisation.

Il s'agit bien évidemment de supports à vocation pédagogique qui **ne sont pas toujours transposables** à une activité professionnelle.

## Pré-requis

Avoir suivi/lu les cours concernant les [inventaires](/blog/cours/ansible/ansible-les-inventaires-statiques) et les [playbooks](/blog/cours/ansible/ansible-les-playbooks) !

Et donc disposer d'un inventaire s'approchant de celui-ci:

```yaml
all:
  hosts:
    vm-web-prod-01:
      ansible_host: XXX.XXX.XXX.XXX
      ansible_user: debian
    vm-db-prod-01:
      ansible_host: XXX.XXX.XXX.XXX
      ansible_user: debian
    vm-web-staging-01:
      ansible_host: XXX.XXX.XXX.XXX
      ansible_user: debian
    vm-db-staging-01:
      ansible_host: XXX.XXX.XXX.XXX
      ansible_user: debian
  children:
    webservers:
      hosts:
        vm-web-prod-01: ~
        vm-web-staging-01: ~
    dbservers:
      hosts:
        vm-db-prod-01: ~
        vm-db-staging-01: ~
    staging:
      hosts:
        vm-web-staging-01: ~
        vm-db-staging-01: ~
    production:
      hosts:
        vm-web-prod-01: ~
        vm-db-prod-01: ~
```

## Introduction

Nous avons vu précemment comment débuter avec Ansible avec les notions fondamentales d'inventaire et de playbooks. Dans l'idée d'apporter un peu plus de matière à cet ensemble nous allons à présent aborder la notion de variables.

## Les variables

Ansible introduit énormément de **souplesse** en terme « d'endroits » où peuvent être déclarées des variables ce qui laisse **énormémement de liberté** sur la façon dont on peut organiser un projet. En contrepartie cela requiert de la **rigueur** afin de respecter les standards établis pour un projet donné, sous peine que cela devienne très rapidement un vrai foutoir.

### Les variables d'hôte

Pour commencer nous aborderons le principe des variables d'hôtes qui en toute logique, permettent de définir des variables au niveau d'une machine bien précise. Vous verrez avec le temps que sur des infrastructures d'exploitation conséquentes ces variables sont souvent peu utilisées car il est rare de n'avoir qu'une seule machine derrière un service.

Commençons par créer un nouveau répertoire `host_vars` à la racine de notre répertoire de travail qui contiendra des fichiers **reprenant les nom d'hôtes définis dans notre inventaire**, nous débuterons par les membres du groupe `webservers` et créerons donc 2 fichiers `vm-web-prod-01.yml` et `vm-web-staging-01.yml`.

Chaque fichier contiendra pour l'instant une définition de variable selon l'exemple suivant:

```yaml
hostname: web-production-01
```
On fera bien évidemment de même avec le second fichier et les instances du groupe `dbservers` en ajoutant les fichiers `vm-db-prod-01.yml` et `vm-db-staging-01.yml` contenant respectivement:

```yaml
hostname: db-production-01
```

et

```yaml
hostname: db-staging-01
```
Il est possible de faire afficher à Ansible les différentes variables définies via `ansible-inventory --graph -i inventories --vars` qui devrait vous renvoyer pour l'instant:

```yaml
@all:
  |--@ungrouped:
  |--@webservers:
  |  |--vm-web-prod-01
  |  |  |--{ansible_host = XXX.XXX.XXX.XXX}
  |  |  |--{ansible_user = debian}
  |  |  |--{hostname = web-production-01}
  |  |--vm-web-staging-01
  |  |  |--{ansible_host = XXX.XXX.XXX.XXX}
  |  |  |--{ansible_user = debian}
  |  |  |--{hostname = web-staging-01}
  |--@dbservers:
  |  |--vm-db-prod-01
  |  |  |--{ansible_host = XXX.XXX.XXX.XXX}
  |  |  |--{ansible_user = debian}
  |  |  |--{hostname = db-prod-01}
  |  |--vm-db-staging-01
  |  |  |--{ansible_host = XXX.XXX.XXX.XXX}
  |  |  |--{ansible_user = debian}
  |  |  |--{hostname = db-staging-01}
  |--@staging:
  |  |--vm-web-staging-01
  |  |  |--{ansible_host = XXX.XXX.XXX.XXX0}
  |  |  |--{ansible_user = debian}
  |  |  |--{hostname = web-staging-01}
  |  |--vm-db-staging-01
  |  |  |--{ansible_host = XXX.XXX.XXX.XXX}
  |  |  |--{ansible_user = debian}
  |  |  |--{hostname = db-staging-01}
  |--@production:
  |  |--vm-web-prod-01
  |  |  |--{ansible_host = XXX.XXX.XXX.XXX}
  |  |  |--{ansible_user = debian}
  |  |  |--{hostname = web-production-01}
  |  |--vm-db-prod-01
  |  |  |--{ansible_host = XXX.XXX.XXX.XXX}
  |  |  |--{ansible_user = debian}
  |  |  |--{hostname = db-prod-01}
```

Où l'on peut constater la présence de nos variables au niveau de chacun des hôtes !

### Les variables de groupe

Passons à présent aux variables de groupes, vous l'aurez compris celles-ci s'appliqueront à un groupe de machines **tel que nous l'aurons défini dans notre inventaire**.
Leur fonctionnement repose sur le même principe que les variables d'hôtes, nous créerons donc cette fois un répertoire appelé `group_vars` contenant un fichier pour chacun des groupes que nous aurons défini.

Nous allons donc créer les fichiers `production.yml` et `staging.yml` contenant respectivement pour l'instant:

```yaml
stage: production
```
et
```yaml
stage: staging
```

En rejouant la commande ansible précédente vous pourrez constater qu'une nouvelle variable `stage` apparait bien comme définie lors de l'affichage de votre inventaire.

!!! info "L'héritage des variables"
    Il est bien évidemment possible d'appliquer une définition de variable aux groupes parents comme aux groupes enfants, dans ce cas on prendra bien soin de faire attention à l'héritage des variables ! 

On oubliera pas au passage que même s'il n'apparait pas de manière explicite dans notre inventaire, le groupe `all` est systématiquement défini par Ansible comme « super parent » et qu'il est donc bien évidemment possible de déclarer des variables pour ce groupe en créant un fichier `all.yml` dans `group_vars` contenant par exemple: 

```yaml
workshop: ansible
```

### Définir des variables dans un inventaire

Il est possible (je vous l'ai dit Ansible est très souple) de définir des variables directement dans votre fichier d'inventaire, on l'a déjà plus ou moins vu d'ailleurs avec la définition de clés spécifiques à Ansible comme `ansible_host` ou `ansible_user` au niveau d'un hôte, l'ajout de variables sur un hôte fonctionne donc de la même façon en ajoutant des clés à la suite.

Au niveau d'un groupe, il faudra passer par la clé `vars`. Par exemple on pourrait imaginer par exemple indiquer un serveur de temps bien précis pour une zone géographique avec quelque chose comme: 

```yaml
france:
  hosts:
    host-01:
    host-02:
  vars:
    ntp_server: ntp.univ-lyon1.fr
```

### Comment les variables sont elles fusionnées ?

Ansible fusionne les variables pour les appliquer de manière spécifique à chacun de nos hôtes, cela signifie que sortie de notre définition d'inventaire et de correspondance hôte/groupe la notion de groupe ne perdure pas, en effet Ansible va écraser les variables préalablement définies en suivant cet ordre (de poids le plus faible au plus important):

- groupe `all` (n'oubliez pas c'est le parent « racine »)
- groupe parent
- groupe enfant
- hôte

Pour résumer de la variable la moins précise (en terme de périmètre de définition) à la plus précise.

**Quelques points d'attention toutefois:**

Pour les groupes de même niveau hiérarchique les variables du dernier groupe considéré écraseront les autres, sauf si une pondération est appliquée au niveau du groupe en utilisant la variable `ansible_group_priority` comme suit:

```yaml
france:
  hosts:
    host-01:
    host-02:
  vars:
    ntp_server: ntp.sophia.mines-paristech.fr
lyon:
  hosts:
    host-01:
    host-02:
  vars:
    ntp_server: ntp.univ-lyon1.fr
    ansible_group_priority: 10
```

!!! info "Prioriser un groupe"
    `ansible_group_priority` peut uniquement être défini au niveau de l'inventaire, il n'est pas possible de l'utiliser dans `group_vars`.

### Last but not least, les facts

Les facts sont au coeur du fonctionnement des variables dans Ansible dans le sens où ce sont des variables spécifiques récupérées directement depuis l'hôte concerné par un déploiement.
Elles permettent de récupérer pas mal d'information parmi lesquelles notamment la ou les interfaces réseaux des machines, le type d'OS et sa version ou encore des informations concernant le matériel de la cible.

On peut les consulter en utilisant par exemple le module `setup` directement en ligne de commande: `ansible -i inventories vm-web-prod-01 -m setup`.
Il est possible de filtrer la sortie affichée par Ansible en utilisant l'option `filter`: `ansible -i inventories vm-web-prod-01 -m setup -a "filter=ansible_default_ipv4"`

Ces « facts » se révèleront fort utiles au fur et à mesure de votre prise en main d'Ansible.

## Utiliser les variables dans nos playbooks

Nous avons vu comment définir des variables, c'est bien beau mais comment les utiliser ? 

Reprenons par exemple nos fichiers d'hôtes ou nous définissons la clé `hostname`, on peut constater que celle-ci dispose d'une partie qui reprend le contenu de la clé `stage` définie au niveau du groupe.
On peut donc modifier ces fichiers d'hôtes de la manière suivante pour exploiter cette définition:

```yaml
hostname: "web-{{ stage }}-01"
```

Allons ensuite modifier notre playbook (`webservers.yml`) pour utiliser ces variables de la manière suivante:

```yaml
---
- hosts: webservers

  pre_tasks:
    - name: Updating APT cache index
      ansible.builtin.apt:
        update_cache: yes

  tasks:
    # NGINX
    - name: Install Nginx web server
      ansible.builtin.apt:
        name: nginx
        state: present
      
    - name: Nginx status configuration file
      ansible.builtin.copy:
        src: nginx/status.conf
        dest: /etc/nginx/conf.d/status.conf
      notify:
          - restart_nginx

    # CONFIG
    - name: Set a hostname
      ansible.builtin.hostname:
        name: "{{ hostname }}"

  handlers:
    - name: restart_nginx
      ansible.builtin.service:
        name: nginx
        state: restarted
```

## Les templates

Sujet étroitement lié à l'utilisation des variables, les templates au sens d'Ansible sont des fichiers un peu particuliers dont le contenu peut-être défini dynamiquement (par opposition notamment à l'utilisation de fichiers de configuration « statiques ») comme nous avons pu le voir dans la partie [playbook](/blog/cours/ansible/ansible-les-playbooks#les-handlers).

Il faut également savoir qu'Ansible s'appuie sur le moteur de template [Jinja2](https://jinja.palletsprojects.com/en/3.1.x/) issu du monde Python qui pourrait être comparé à Twig pour PHP, Pebble pour Java, Liquid pour RoR ou encore DotLiquid pour .Net.

Nous l'avons vu les variables peuvent être définies à divers endroits et peuvent ensuite être accessibles avec les doubles parenthèses `{{ ... }}`.

Imaginons que nous souhaitions personnaliser nos [motd](https://fr.wikipedia.org/wiki/Message_of_the_Day) par exemple en fonction de l'environnement duquel fait partie notre hôte cible.

### Définir et utiliser un nouveau template

Ansible prévoit par défaut l'utilisation d'un répertoire `templates` pour cette mission, que nous devrons donc créer (toujours à la racine de notre répertoire de travail).
Afin de stocker 2 fichiers nous créerons un répertoire dédié à motd qui contiendra donc `production.j2` et `staging.j2`

Pour ces deux nouveaux fichiers nous ajouterons les contenus suivants:

Pour la **production**:

```shell
#!/bin/sh

MOTD=$(cat <<'EOF'
[38;5;28m                    ____[0m[0m
[38;5;28m                 _.' :  `._[0m
[38;5;28m             .-.'`.  ;   .'`.-.[0m
[38;5;28m    __      / : ___\ ;  /___ ; \      __[0m
[38;5;28m  ,'_ ""--.:__;".-.";: :".-.":__;.--"" _`,[0m
[38;5;28m  :' `.t""--.. '[38;5;241m<[38;5;234m@[38;5;241m,[38;5;28m`;_  '[38;5;241m,[38;5;234m@[38;5;241m>[38;5;28m` ..--""j.' `;[0m
[38;5;28m       `:-.._J '-.-'L__ `-- ' L_..-;'[0m
[38;5;28m         "-.__ ;  .-"  "-.  : __.-"[0m
[38;5;28m             L ' /.------.\ ' J[0m
[38;5;28m              "-.   "--"   .-"[0m
[38;5;230m             [0m[38;5;94m__[38;5;28m.l"-:_JL_;-";.[0m[38;5;94m__[0m
[38;5;230m          .-j[0m[38;5;94m/'.[38;5;28m;  ;""""  /[0m[38;5;94m .'[0m[38;5;230m\"-.[0m
[38;5;230m        .' /:[0m[38;5;94m`. "-.[38;5;28m:    :[0m[38;5;94m.-" .'[0m[38;5;230m;  `.[0m
[38;5;230m     .-"  / ;[0m[38;5;94m  "-. "-..-" .-"[0m[38;5;230m  :    "-.[0m
[38;5;230m  .+"-.  : : [0m[38;5;94m     "-.__.-"[0m[38;5;230m      ;-._   \[0m
[38;5;230m  ; \  `.; ;                    : : "+. ;[0m
[38;5;230m  :  ;   ; ;                    : ;  : \:[0m
[38;5;230m : `."-; ;  ;                  :  ;   ,/;[0m
[38;5;230m  ;    -: ;  :                ;  : .-"'  :[0m
[38;5;230m  :\     \  : ;             : \.-"      :[0m
[38;5;230m   ;`.    \  ; :            ;.'_..--  / ;[0m
[38;5;230m   :  "-.  "-:  ;          :/."      .'  :[0m
[38;5;230m     \       .-`.\        /t-""  ":-+.   :[0m
[38;5;230m      `.  .-"    `l    [0m[38;5;28m__/ /[0m[38;5;94m`. :  ; ;[0m[38;5;230m \  ;[0m
[38;5;230m        \   .-" .-"[0m[38;5;28m-.-"  .' .'[0m[38;5;94mj \  /[0m[38;5;230m   ;/[0m
[38;5;230m         \ / .-"[0m[38;5;28m   /.     .'.' [0m[38;5;94m;_:'[0m[38;5;230m    ;[0m
[38;5;230m          :-""[0m[38;5;28m-.`./-.'     /[0m[38;5;230m    `.___.'[0m
[38;5;28m                \ `t  ._  /[0m
[38;5;28m                 "-.t-._:'[0m

{{ motd.message | center(42) }}

EOF
)

printf "${MOTD}\n\n\n"
```

Pour la **staging**:

```shell
#!/bin/sh
{% set message = motd.message -%}

MOTD=$(cat <<'EOF'
 -{% for i in range(message | length) %}-{% endfor %}-
< {{ message }} >
 -{% for i in range(message | length) %}-{% endfor %}-
        \   ^__^
         \  (oo)\_______
            (__)\       )\/\\
                ||----w |
                ||     ||

EOF
)

printf "${MOTD}\n\n\n"
```

Vous devriez à ce stade disposer d'une arborescence ressemblant à ça:

```
|-- example.yml
|-- files
|   `-- nginx
|       `-- status.conf
|-- group_vars
|   |-- all.yml
|   |-- production.yml
|   `-- staging.yml
|-- host_vars
|   |-- vm-db-prod-01.yml
|   |-- vm-db-staging-01.yml
|   |-- vm-web-prod-01.yml
|   `-- vm-web-staging-01.yml
|-- inventories
|   `-- hosts.yml
`-- templates
    `-- motd
        |-- production.j2
        `-- staging.j2
```

Après avoir ajouté le contenu de chacun de ces motd nous allons exploiter ces 2 templates en modifiant notre playbook de la façon suivante:

```yaml
    # MOTD
    - name: MOTD > Installing template
      ansible.builtin.template:
        src: "motd/{{stage}}.j2"
        dest: "/etc/update-motd.d/10-welcome"
        owner: root
        group: root
        mode: "0755"
```

Vous l'aurez sans doute compris, nous allons déployer un motd différent en fonction de notre environnement, l'idée étant d'afficher de manière flagrante à la connexion si nous arrivons sur un environnement de production ou de staging.

### Variabiliser ses fichiers de configuration

Avec cette première approche vous avez sans doute vu les possibilités qui s'offrent à nous, nous avons vu pour l'instant la possibilité de déposer des fichiers de manière « statique » à savoir sans données spécifiques à un hôte et un groupe.
C'est justement ce qu'apporte les templates nous allons pouvoir définir des variables dont la valeur dépendra de l'environnement d'exécution ou de la finalité de nos serveurs.

Introduisons à présent un service PHP-FPM qui nous permettra de faire tourner des applicatifs basés sur PHP.

Toujours dans notre playbooks `webservers` ajoutons une section pour PHP où nous gérons:

- l'installation du paquet;
- l'ajout d'un fichier de configuration personnalisé;
- le redémarrage du service.

```yaml
    # PHP
    - name: Install PHP-FPM service
      ansible.builtin.apt:
        name: php-fpm
        state: present
    - name: PHP-FPM > Configuration
      ansible.builtin.template:
        src: "php/app.ini.j2"
        dest: "/etc/php/8.2/fpm/app.ini"  
  
  handlers:
    - name: restart_php-fpm
      ansible.builtin.service:
        name: php8.2-fpm
        state: restarted
```

Si jamais vous avez oublié comment fonctionnait les handlers (ou à quoi ils servent) vous pouvez vous référer à la partie [playbooks](/blog/cours/ansible/ansible-les-playbooks).

Il nous restera à créer notre template dans le répertoire `templates/php` portant le nom `app.ini.j2`, le nommage importe peu mais il peut donner une idée de l'utilité du fichier en question, dans notre cas la double extension `.ini.j2` nous permet d'indiquer qu'il s'agit d'un template Jinja2 et que son contenu est au format ini.

Profitons en pour introduire les structures de contrôle à l'intérieur de templates Jinja2.

Le contenu de notre fichier `app.ini.j2` sera ainsi:

```
{# PHP custom configuration, handle by Ansible #} 
{%- set config = php.config|default({}) -%}

{% for key, value in config.items() %}
  {{ key }} = {{ value }}
{% endfor %}
```

**On y remarquera trois choses:**

- Les commentaires sont déclarés à l'aide de `{#...#}`;
- Il est possible de déclarer des variables à l'intérieur d'un template Jinja2 à l'aide de la structure `{%- set variable_name = value -%}`;
- On utilise pour l'exemple une structure itérative `{% for %}...{% endfor %}`.

Les structures de contrôles dans la syntaxe jinja2 sont exprimées à l'aide des marqueurs `{% ... %}`

Il nous reste a alimenter notre template à partir de nos fichiers de déclaration de variables, ici `group_vars/webservers.yml` qui contiendra:

```yaml
php:
  config:
    error_reporting: 'E_ALL & ~E_DEPRECATED & ~E_STRICT'
    display_errors: False
    memory_limit: 256M
```

### Les filtres Jinja2

Jinja2 propose nativement un certain nombre de « [filtres](https://jinja.palletsprojects.com/en/2.10.x/templates/#list-of-builtin-filters) » permettant de faire des manipulations basiques à l'intérieur de nos templates.
Afin d'appliquer un filtre à une valeur on utilise la notation `|`.

**Exemple:**

```
{{ "lyon" | capitalize }}
```
renverra

```
# output
Lyon
```

Ci-dessous un playbook utilisant différents filtres (ne pas hésiter à tester dans un playbook dédié `jinjaFilters.yml` ;) )

```yaml
- name: "Jinja Filters Playbook"
  hosts: localhost
  gather_facts: no
  vars:
    mandala: element
      
  tasks:
    - name: Mandala variable is mandory
      ansible.builtin.debug:
        msg: "{{ mandala | mandatory }}"
    
    - name: Undefined variable have a default
      ansible.builtin.debug:
        msg: "{{ undefined_var | default('default') }}"

    - name: Omitting a parameters
      ansible.builtin.debug:
        msg: "{{ va | default(omit) }}" #If omitted, prints a generic message.

    - name: Flatten a list
      ansible.builtin.debug:
        msg: "{{ [3, [4, 2] ] | flatten }}"
    
    - name: Join two list with '+'
      ansible.builtin.debug:
        msg: "{{ [3, 4] + [4, 2] }}"

    - name: Join two list with | union()
      ansible.builtin.debug:
        msg: "{{ [3, 4] | union([4, 2]) }}"

    - name: Hash a string
      ansible.builtin.debug:
        msg: "{{ 'secret' | hash }}"

    - name: Hash a password
      ansible.builtin.debug:
        msg: "{{ 'secret' | password_hash }}"

    - name: Combine hashes
      ansible.builtin.debug:
        msg: "{{ {'param1': ['value1', 'value3']} | combine({'param2': 'value2'}) }}"

    - name: Url split
      ansible.builtin.debug:
        msg: "{{ 'https://user:password@www.example.com:9000/dir/index.html?query=term#fragment' | urlsplit }}"
        
    - name: Display date time
      ansible.builtin.debug:
        msg: "{{ '%Y-%m-%d %H:%M:%S' | strftime }}"

    - name: "Multi Filter : Play with datetime objet to get minutes from now to end of this course"
      ansible.builtin.debug:
        msg: "{{ ((('%Y-%m-%d %H:%M:%S' | strftime | to_datetime) -  ('%Y-%m-%d 18:00:00' | strftime | to_datetime)).total_seconds() / 60) | abs }}"
```

### Conditionner l'exécution de ses tâches

Il est également possible de venir utiliser des structures conditionnelles à l'intérieur de nos playbooks et de par exemple, conditionner l'exécution de certaines tâches à un contexte particulier.
On peut ainsi imaginer retreintre l'utilisation des tâches utilisant le module apt aux seules distributions Debian.

Nous le ferions par exemple en ajoutant une condition sur toutes nos tâches faisant appel au module de la façon suivante:

```yaml
  pre_tasks:
    - name: Updating APT cache index
      ansible.builtin.apt:
        update_cache: yes
      when: ansible_distribution == "Debian"
```

On remarquera que la condition `when` contient une expression Jinja « brute » sans `{{ ... }}`
On pourra utiliser différents opérateurs parmi les suivants:

- http://jinja.pocoo.org/docs/templates/#comparisons
- http://jinja.pocoo.org/docs/templates/#logic
- http://jinja.pocoo.org/docs/templates/#other-operators

**Application pratique :** Nous allons **conditionner l'exécution** de la partie PHP, on peut en effet imaginer avoir des serveurs web ne servant que du contenu statique et qui n'auront donc pas forcément besoin de PHP.

Modifions nos deux tâches concernant PHP comme suit (pour rappel dans `webservers.yml`): 

```yaml
...
    # PHP
    - name: Install PHP-FPM service
      ansible.builtin.apt:
        name: php-fpm
        state: present
      when: php.enabled
      tags:
        - php
        - installation

    - name: PHP-FPM > Configuration
      ansible.builtin.template:
        src: "php/app.ini.j2"
        dest: "/etc/php/8.2/fpm/conf.d/app.ini"    
      notify:
          - restart_php-fpm
      when: php.enabled
      tags:
        - php
        - configuration
...
```

Et notre fichier de variables de groupe `group_vars/webservers.yml`:

```yaml
php:
  enabled: false
  ...
```

En jouant sur l'état de notre variable `php.enabled` nous pouvons donc activer / désactiver l'exécution de nos tâches PHP ce qui donnera: 

<figure>
    <img src="content/images/blog/2024/cours/ansible/ansible-variables/ansible-playbook-conditionnal.png">
    <figcaption>
      <span class="figure__legend">Conditionnement d'exécution des tâches.</span>
    </figcaption>
</figure>

## Point de progression

Nous avons à présent entre nos mains la quasi totalité des concepts fondamentaux d'Ansible

- L'installation d'un [environnement Ansible](/blog/cours/ansible/ansible-environnement-cle-en-main);
- Utiliser l'[inventaire](/blog/cours/ansible/ansible-les-inventaires-statiques);
- Créer des [playbooks](/blog/cours/ansible/ansible-les-playbooks).

La prochaine étape sera orientée sur la réutilisation, l'optimisation et la structuration de ces concepts en introduisant la notion de **roles/collections**.

- https://docs.ansible.com/ansible/latest/inventory_guide/intro_inventory.html#assigning-a-variable-to-one-machine-host-variables
- https://docs.ansible.com/ansible/latest/inventory_guide/intro_inventory.html#assigning-a-variable-to-many-machines-group-variables
- https://jinja.palletsprojects.com/en/3.1.x/