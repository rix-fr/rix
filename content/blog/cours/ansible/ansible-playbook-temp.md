---
type:               "post"
title:              "Ansible - playbooks."
date:               "2023-12-7"
lastModified:       ~
tableOfContent:     true
description:        "Dans ce premier cours à destination des étudiants et/ou néophytes, nous verrons ce qu'est Ansible ainsi qu'un exemple très simple de son utilisation."

thumbnail:          "content/images/blog/thumbnails/ansible-premier-pas.jpg"
tags:               ["cours", "ansible", "automation"]
categories:         ["cours"]
authors:            ["gfaivre"]
---

En exécutant cette commande vous devriez obtenir la sortie suivante ou équivalente (si tout se passe bien).

<figure>
    <img src="content/images/blog/2023/ansible/ansible-playbook/ansible-playbook-ping.png">
    <figcaption>
      <span class="figure__legend">Utilisation du module ping dans un playbook.</span>
    </figcaption>
</figure>

Nous venons donc de faire appel au module ping mais à l'intérieur d'un playbook. Ce que nous avons en retour et une sortie type d'Ansible sur laquelle on peut remarquer quelques informations toujours intéressantes (tout en bas) avec notamment:

- `ok`: Le nombre de tâches (tasks) qui se sont correctement exécutées;
- `changed`: Le nombre de changements opérés sur nos machines cibles;
- `unreachable`: Le nombre de machine injoignable (par soucis de réseau par exemple);
- `failed`: Le nombre de tâches en erreur;
- `skipped`: Le nombre de tâches qui ont été ignorées (par exemple si une tâches ne remplie pas certaines conditions prédéfinies).

Il est bon de noté également qu'un playbook peut contenir plusieurs « plays » nous pouvons donc avoir des tâches attribuées à différents groupe cible comme ceci: 

```yaml
# in file ping.yml
---
- hosts: webservers

  tasks:
    - name: Check if host is alive # Description of the task
      ansible.builtin.ping:

- hosts: dbservers

  tasks:
    - name: Get stats of a file
      ansible.builtin.stat:
        path: /etc/hosts
```

Nous avons dans ce cas de figure deux groupes concernés chacun par une « tâche »:

- Le groupe `webservers` (contenant donc 2 machines) sur lequel nous exécutons le module ping;
- Le groupe `dbservers` (contenant également 2 machines) sur lequel nous exécutons le module `stat`.

!!! info "Le module stat"
    Le module stat permet d'exécuter la commande système `stat` sur un fichier du serveur cible. Il prends différents paramètres que l'on peut retrouver sur la [documentation officielle](https://docs.ansible.com/ansible/latest/collections/ansible/builtin/stat_module.html) dont le paramètre `path` qui lui est obligatoire.

Rappelez-vous également qu'un « plays » peut avoir à exécuter plusieurs tâches les unes à la suite des autres, exemple: 

```yaml
# in file ping.yml
---
- hosts: webservers

  tasks:
    - name: Check if host is alive # Description of the task
      ansible.builtin.ping:
    - name: Execute a simple command
      ansible.builtin.shell: echo "Ansible was here !" > ansible.txt
        
- hosts: dbservers

  tasks:
    - name: Get stats of a file
      ansible.builtin.stat:
        path: /etc/hosts
```

Vous devriez, en jouant votre playbook (`ansible-playbook ping -i inventories`) obtenir une sortie équivalente à:

