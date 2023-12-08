---
type:               "post"
title:              "Ansible - Les inventaires statiques"
date:               "2023-11-27"
lastModified:       ~
tableOfContent:     true
description:        "Première étape vers l'utilisation d'Ansible, les inventaires. Ils sont le point d'entrée vers vos infras et sont donc central au pilotage de vos instances / serveurs."
thumbnail:          "content/images/blog/thumbnails/ansible-inventaire.jpg"
tags:               ["cours", "ansible", "automation"]
categories:         ["cours"]
authors:            ["gfaivre"]
---

## Préambule

Ce cours est utilisé dans le cadre de TP au sein de l'IUT Lyon 1. Il est notamment dispensé à des étudiants peu ou pas familiers avec les stratégies d'automatisation et de déploiement des infrastructures.
Bien que très axé débutants il peut également représenté une possibilité de monter « rapidement » pour certaines équipes sur les principes fondamentaux d'Ansible afin de disposer du bagage minimal nécessaire à son utilisation.

Il s'agit bien évidemment de supports à vocation pédagogique qui **ne sont pas toujours transposables** à une activité professionnelle.

## Pré-requis

Disposer d'un **environnement de travail Ansible fonctionnel**, si ça n'est pas encore le cas vous pouvez jeter un oeil [ici](/blog/cours/ansible/ansible-premiers-pas) !

## Introduction

Afin de pouvoir attaquer nos différentes machines, Ansible a besoin d'un référentiel de celles-ci avec un minimum d'informations les concernants (histoire de savoir comment s'y connecter par exemple ;)).

C'est là qu'entre en jeu **les inventaires**. Il existe deux façons de constituer des inventaires, la première est **manuelle**, et consiste à écrire ni plus ni moins la liste des machines que l'on souhaites manager on parle dans ce cas d'**inventaire statique**.

La deuxième méthode introduit un principe de « reconnaissance » des machines disponibles, dans ce cas de figure on constituera nos inventaires de manière automatique, on parle dans ce cas d'**inventaires dynamiques** que nous verrons plus tard.

Les inventaires permettent également de structurer / hiérarchiser nos machines en utilisant une notion de **groupe**.
Ansible propose plusieurs plugins capablent de gérer des inventaires de machines, ils sont consultables à l'aide de la commande: 

```
ansible-doc -t inventory -l
```

Qui devrait vous renvoyer la liste suivante: 

```
02:48:02 lazy@ansible_env lazy → ansible-doc -t inventory -l
ansible.builtin.advanced_host_list Parses a 'host list' with ranges                                                                    
ansible.builtin.auto               Loads and executes an inventory plugin specified in a YAML config                                   
ansible.builtin.constructed        Uses Jinja2 to construct vars and groups based on existing inventory                                
ansible.builtin.generator          Uses Jinja2 to construct hosts and groups from patterns                                             
ansible.builtin.host_list          Parses a 'host list' string                                                                         
ansible.builtin.ini                Uses an Ansible INI file as inventory source                                                        
ansible.builtin.script             Executes an inventory script that returns JSON                                                      
ansible.builtin.toml               Uses a specific TOML file as an inventory source                                                    
ansible.builtin.yaml               Uses a specific YAML file as an inventory source  
```

Dans notre cas nous nous appuyerons essentiellement sur le plugin `yaml`.

## Structurer ses inventaires

Un inventaire n'est en fait ni plus ni moins qu'un ou plusieurs fichiers contenant des informations concernant le parc de machines que l'on souhaite piloter.

En terme de structure vous rencontrerez énormément de façons de faire, celles-ci étant bien évidemment guider par le **besoin métier**, on pourra citer comme contraintes par exemple:

- La taille des infrastructures (le nombre d'éléments qui la constitue);
- Leur localisation géographique (pays, ville...);
- Le besoin d'adresser finement un groupe de machines et pas un autre...

Bref tout est imaginable à ce niveau.
En ce qui nous concerne nous interviendrons sur un parc plutôt modeste puisque pour nos travaux nous utiliserons au maximum 4 machines.

Nous allons donc commencer par créer un répertoire qui leur sera dédié appelé `inventories` nous déplacerons ensuite le fichier `hosts.yml` que nous avions créé [précédemment](/blog/cours/ansible/ansible-premiers-pas#communication-ansible-serveurs-distants).

**Vous devriez donc disposer d'une arborence similaire à la suivante:** 

```
ansible/
├── inventories
│   └── hosts.yml
└── Makefile
```

## La configuration d'un inventaire

Pour rappel le contenu de votre fichier `hosts.yml` doit pour l'heure être le suivant:

```yaml
all:
  hosts:
    vm-web-prod-01:
      ansible_host: XXX.XXX.XXX.XXX
      ansible_user: debian
```

### Clés génériques

La structure du fichier nous permet de mettre en évidence deux clés essentielles:

- `ansible_host`: Le nom résolvable ou l'adresse IP de la machine distante;
- `ansible_user`: L'utilisateur à utiliser pour ouvrir une session sur cette même machine.

Il est toutefois possible d'utiliser d'autres clés de configuration pour enrichir la définition de notre machine comme:

- `ansible_port`: Permet de spécifier le port de connexion SSH (si différent du port standard, pour rappel le port par défaut est **22**)

### Clés spécifiques à SSH

- `ansible_ssh_pass`: Le mot de passe du compte SSH utilisé (on lui préferera une **authentification par clés**);
- `ansible_ssh_private_key_file`: **Le chemin** vers la clé à utiliser pour se connecter au compte SSH;
- `ansible_ssh_extra_args`: Permet d'**ajouter des options supplémentaires** à la ligne de commande SSH utilisée par Ansible.

### Clés spécifiques à l'escalade de privilèges

- `ansible_become`: Permet de forcer l'escalade de privilèges;
- `ansible_become_method`: Permet de spécifier la méthode d'escalade des privilèges;
- `ansible_become_user`: Permet de spécifier l'utilisateur cible de l'escalade de privilèges;
- `ansible_become_pass`: Permet de spécifier le mot de passe de l'utilisateur cible de l'escalade de privilèges (encore une fois, on préfera la méthode par clés SSH).

## Enrichir son inventaire

À présent que nous avons effectuer un petit tour rapide du propriétaire, nous allons « étoffer » notre inventaire initial en ajoutant une deuxième machine comme ci-dessous:

```yaml
# Fichier hosts.yml
all:
  hosts:
    vm-web-prod-01:
      ansible_host: XXX.XXX.XXX.XXX
      ansible_user: debian
    vm-web-staging-01:
      ansible_host: XXX.XXX.XXX.XXX
      ansible_user: debian
```

En ajoutant une machine et en jouant la commande `ansible -i inventories/hosts.yml all -m ping` nous devrions voir qu'ansible considère bien nos deux machines: 

<figure>
    <img src="content/images/blog/2023/ansible/ansible-inventaires/ping.gif">
    <figcaption>
      <span class="figure__legend">Utilisation du module ping sur deux machines.</span>
    </figcaption>
</figure>

!!! info "Hôte local"
    Il est bien évidemment possible d'interroger notre propre machine à l'aide d'Ansible, en modifiant relativement simplement notre fichier d'inventaire. Attention toutefois à ce que vous faites puisque vous pouvez directement impacter la configuration et donc le fonctionnement de votre machine.

**Exemple de fichier d'inventaire pour piloter une machine locale:**

```yaml
all:
  hosts:
    localhost:
      ansible_host: 127.0.0.1
      ansible_connection: local
```

### Définir des groupes de machines

Pour l'exemple nous allons créer un fichier `groups.yml` (toujours dans `inventories`) contenant: 

```yaml
all:
  children:
    webservers:
      hosts:
        vm-web-prod-01: ~
        vm-web-staging-01: ~
```

**ATTENTION**: `children` est une sous clé de `all` ;)

### La commande « ansible-inventory »

Ansible propose différentes commandes parfois très spécifiques, l'occasion de tester notre configuration d'inventaire !

Testons: `ansible-inventory --list -i inventories`

On notera que cette fois-ci nous donnons le répertoire `inventories` en paramètre.

**Cette commande devrait vous afficher la sortie suivante (format json):**

```json
{
    "_meta": {
        "hostvars": {
            "vm-web-prod-01": {
                "ansible_host": "192.168.140.XXX",
                "ansible_user": "debian"
            },
            "vm-web-staging-01": {
                "ansible_host": "192.168.140.XXX",
                "ansible_user": "debian"
            }
        }
    },
    "all": {
        "children": [
            "ungrouped",
            "webservers"
        ]
    },
    "webservers": {
        "hosts": [
            "vm-web-prod-01",
            "vm-web-staging-01"
        ]
    }
}
```

ou encore avec l'option `--graph` en lieu et place de `--list` qui est plus parlante visuellement `ansible-inventory --graph -i inventories`:

```
@all:
  |--@ungrouped:
  |--@webservers:
  |  |--vm-web-prod-01
  |  |--vm-web-staging-01
```

**À retenir:**
Ansible utilise une arborescence ou figurera **toujours**:

- Un groupe `all`: C'est le groupe racine auquel appartiendra **toutes vos machines sans exception** (On remarquera avec cette information que lorsque nous avons utilisé la commande `ansible -i inventories/hosts.yml all -m ping`, **all** indiquait donc le groupe cible).
- Un groupe `ungrouped`: Groupe auquel sera affectée toute machine **n'appartenant à aucun groupe** (exception faites de **all** bien évidemment);

Dans notre exemple ci-dessus on voit donc bien que nos deux machines font bien partie du groupe `webservers`.

!!! info "Fichiers d'inventaire multiples"
    Si l'option `-i` d'Ansible prend un fichier d'inventaire en paramètre elle peut également prendre un répertoire et dans ce cas Ansible considérera l'ensemble des fichiers présents dans le répertoire.
    Ce fonctionnement offre la possibilité avec des infrastructures composées de nombreuses machines de pouvoir les séparer dans plusieurs fichiers en fonction de différents critères.

### Ordre de chargement des inventaires

Vous aurez compris que si l'on aborde le sujet c'est qu'il est d'importance... pour l'illustrer créons un nouveau fichier d'inventaire que l'on nommera `misc.yml` contenant:

```yaml
all:
  hosts:
    vm-web-prod-01:
      ansible_host: XXX.XXX.XXX.XXX
      ansible_user: debian
      ansible_port: 22
    vm-web-staging-01:
      ansible_host: XXX.XXX.XXX.YYY
```

Une fois ces modifications faites, rejouez la commande `ansible-inventory --list -i inventories` vous devriez constater de subtils changements au niveau des informations que vous affiche Ansible.

```json
{
    "_meta": {
        "hostvars": {
            "vm-web-prod-01": {
                "ansible_host": "192.168.140.12",
                "ansible_port": 22,
                "ansible_user": "debian"
            },
            "vm-web-staging-01": {
                "ansible_host": "192.168.140.10",
                "ansible_user": "debian"
            }
        }
    },
    "all": {
        "children": [
            "ungrouped",
            "webservers"
        ]
    },
    "webservers": {
        "hosts": [
            "vm-web-prod-01",
            "vm-web-staging-01"
        ]
    }
}
```

**On constatera ainsi:**
- L'ajout de la clé `ansible_port` sur notre première instance;
- La modification de l'adresse IP de la seconde.

**Qu'en retenir ?**

L'utilisation du [Yaml](https://yaml.org/) comme langage de définition introduit une **notion d'arborescence** au niveau de vos clés, il faut ainsi voir la définition de votre machine comme un **tableau multidimensionnel indexé**.

```php
array (
  'vm-web-prod-01' => 
  array (
    'ansible_host' => '192.168.140.12',
    'ansible_port' => 22,
    'ansible_user' => 'debian',
  ),
)
```

**On comprendra donc facilement:**

- Que l'ajout d'une clé entraine l'ajout d'un élément à notre tableau pour la clé concernée (Dans notre cas l'ajout de la clé `ansible_port`);
- Que la modification de la valeur d'une clé écrase sa valeur précédente (Dans notre cas la modification de l'IP de notre machine). 

Conclusion: Lorsque l'on utilise des fichiers d'inventaire multiples il vaut bien prendre en compte leur ordonnancement, **la dernière valeur déclarée pour une clé** étant celle qui sera retenu dans notre tableau final.

!!! info "Groupes de groupes"
    La hiérarchie de groupe d'un inventaire peut avoir plusieurs niveaux. Il est donc possible d'avoir de l'imbrication de groupes. Attention toutefois à ne pas en abuser afin de ne vous perdre dans des arborescence trop complexes.

Complétons pour finir notre inventaire `groups.yml` afin d'obtenir le contenu suivant:

```yaml
all:
  children:
    webservers:
      hosts:
        vm-web-prod-01: ~
        vm-web-staging-01: ~
    staging:
      hosts:
        vm-web-staging-01: ~
    production:
      hosts:
        vm-web-prod-01: ~
```

## Exercices

Rapide mise en pratique des inventaires.

### Exercice 1

Reprendre les différents fichiers contenu dans notre répertoire `inventories` et les compiler en un seul et même fichier `hosts.yml`, les autres fichiers ne sont finalement plus utiles et peuvent être supprimés.

Nous compléterons notre inventaire avec deux machines supplémentaires `vm-db-prod-01` et `vm-db-staging-01` appartenant toutes deux au groupe `dbservers` (Attention à les affecter également à leurs groupes d'environnements respectifs).

Souvenez-vous vous pouvez tester un fichier d'inventaire en particulier en le passant en paramètre de la commande `ansible-inventory`: `ansible-inventory --list -i inventories/hosts.yml`.

### Exercice 2

Nous avons vu qu'il existait différent plugin permettant de « lire » un inventaire (si,si au tout début), essayez d'écrire le même inventaire mais à un format différent (format ini par exemple).

### Exercice 3

Revenons à notre fichier `hosts.yml` séparez son contenu en fonction des environnements que nous avons définis (**staging** et **production**)

## Cibler des groupes de machines avec les « patterns » 

Notre infrastructure est modeste, mais vous serez parfois amenés à travailler avec des infrastructures d'envergure et serez dans l'obligation de « cibler » certaines machines ou groupes de machines.
Il est ainsi possible d'indiquer explicitement à Ansible quelles sont les machines à considérer pour une action donnée.

Certains « patterns » sont très simple et vous devriez en reconnaitre certains:

Le « wildcard » `*` par exemple qui désignera n'importe quelle valeur et qui est utilisable au sein d'une valeur de clé `ip` ou `hostname` (`192.168.140.*` ou encore `*.example.com`).

Ceux que vous rencontrerez le plus souvent: `:`, `:&` ou encore `:!`.

### L'opérateur OR

L'opérateur `:` signifiera qu'une machine peut-être dans un groupe **OU** dans un autre, par exemple **staging** ou **production**.

Essayons toujours avec notre module **ping**: `ansible -i inventories/hosts.yml 'staging:production' -m ping`

!!! info "Avec la commande **ansible-inventory**"
    `ansible-inventory -i inventories/hosts.yml --host='webservers:production'`

### L'opérateur AND

L'opérateur `:&` signifiera qu'une machine peut-être dans un groupe **ET** dans un autre, par exemple **webservers** et **production**.

`ansible -i inventories/hosts.yml 'webservers:&production' -m ping`

Cette fois-ci vous ne devriez avoir que la machine `vm-web-prod-01` qui est solicité par Ansible.

!!! info "Avec la commande **ansible-inventory**"
    `ansible-inventory -i inventories/hosts.yml --host='webservers:&production'`

### L'opérateur NOT

L'opérateur `:!` permettra de cibler une machine qui est dans un groupe **mais pas** dans un autre par exemple membre du groupe **webservers** mais non présente dans le groupe **production**.

`ansible -i inventories/hosts.yml 'webservers:!production' -m ping`

Vous devriez ne soliciter cette fois que `vm-web-staging-01`.

!!! info "Avec la commande **ansible-inventory**"
    `ansible-inventory -i inventories/hosts.yml --host='webservers:!production'`

### Combinaisons multiples

Il est bien évidemment possible de combiner les opérateurs prenez toutefois garde aux expressions trop complexes qui gêneront à la compréhension et pourront être source d'erreur ! 

On peut donc imaginer des choses comme cibler les machines du groupe **webservers** OU **staging** mais qui ne **sont pas dans production** (On est d'accord, ça n'a aucune sens c'est pour l'exemple ;)).

`ansible -i inventories/hosts.yml 'webservers:staging:!production' -m ping`

Il est également possible de mixer nom de groupe et nom de machine: `ansible -i inventories/hosts.yml 'webservers:staging:!vm-web-staging-01' -m ping`

!!! info "Avec la commande **ansible-inventory**"
    `ansible-inventory -i inventories/hosts.yml --host='webservers:staging:!vm-web-staging-01'`


## Conclusion

Nous aurons donc vu que les inventaires bien qu'à priori relativement simples, peuvent amener une forme de complexité sur des infrastructures volumineuses, leur organisation peut donc vite devenir stratégique notamment dans l'optique de faciliter la maintenance du parc piloté par Ansible.

Dans la prochaine étape nous aborderons une nouvelle notion d'Ansible, **les playbooks** qui nous permettront d'écrire nos première tâches !

## Aller plus loin avec les sources

- https://docs.ansible.com/ansible/latest/inventory_guide/intro_inventory.html#connecting-to-hosts-behavioral-inventory-parameters
- https://docs.ansible.com/ansible/latest/inventory_guide/intro_patterns.html


