---
type:               "post"
title:              "Principes de base de l'utilisation de clés SSH."
date:               "2023-10-20"
lastModified:       ~
tableOfContent:     true
description:        "Génération, utilisation et cas pratiques d'utilisation de clés SSH."

thumbnail:          "content/images/blog/thumbnails/network_ssh.jpg"
tags:               ["cours", "ssh", "how-to"]
categories:         ["cours"]
authors:            ["gfaivre"]
---

Une clé SSH est un **moyen d'authentification** vers un serveur SSH reposant sur les principes de **[cryptographie asymétrique](https://fr.wikipedia.org/wiki/Cryptographie_asym%C3%A9trique)** et **[d'authentification défi / réponse](https://fr.wikipedia.org/wiki/Authentification_d%C3%A9fi-r%C3%A9ponse)**.

Elle a deux avantages fondamentaux comparativement avec une authentification par couple identifiant/mot de passe:

- Permettre une authentification facilité (plus de mot de passe à mémoriser) et plus rapide (possibilité de rebond de serveur à serveur par exemple).
- Se prémunir des attaques de type [force brute](https://fr.wikipedia.org/wiki/Attaque_par_force_brute)

La très grande majorité des accès serveurs sont aujourd'hui basés sur leur utilisation, au dela de l'aspect fluidité et sécurité, elle ouvre également la possibilité d'authorisation multiple (sur plusieurs serveurs), de révocation et de signature des accès facilités.

!!! info "Secure Shell (SSH)"
    Secure Shell (SSH) est à la fois un programme informatique et un protocole de communication sécurisé.
    Le protocole de connexion impose un échange de clés de chiffrement en début de connexion. Par la suite, tous les segments TCP sont authentifiés et chiffrés. Il devient donc impossible d'utiliser un sniffer pour voir ce que fait l'utilisateur.
    Le protocole SSH a été conçu avec l'objectif de remplacer les différents protocoles non chiffrés comme rlogin, telnet, rcp et rsh.

## Pré-requis

- Avoir un client SSH installé (OpenSSH pour Linux/OSX et Windows à présent ou encore [Putty](https://www.putty.org/) pour Windows)
- Une ligne de commande ( le truc noir dans lequel on tape du texte ;) )

## TL;DR

**Génération d'une paire de clés:**
```
ssh-keygen -t ed25519 -a 150
```

**Se connecter à un serveur distant**

```
ssh -i ~/.ssh/id25519 user@server_address
```

## Génération d'une paire de clés

Le principe de l'authentification par clés repose, comme explicité sur les différents liens ci-dessus, par la création d'une paire de clés asymétriques.
L'une de ces clés sera votre clé **publique** à déployer sur les machines auxquelles vous avez le droit de vous connecter, l'autre, votre clé **privée**. Et comme son nom l'indique, celle-ci est à vous et rien qu'à vous ; elle ne se partage pas. **JAMAIS**.

Deux notions de base avant de se lancer pour bien comprendre ce que l'on fait:

- Il existe plusieurs types d'**algorithmes de signature numérique**, les plus répandus étant **[RSA](https://fr.wikipedia.org/wiki/Chiffrement_RSA)** et **[Ed25519](https://fr.wikipedia.org/wiki/EdDSA)**;
- Il est possible de spécifier la longueur de vos clés, ce paramètre est essentiel à leur **robustesse**.

Il est recommandé, à la date de rédaction de cet article, d'utiliser l'algorithme **Ed25519** qui a plusieurs avantages comparativement à RSA:

- Robustesse accrue;
- Plus petite taille de clés;
- Génération des clés plus rapide.

```
ssh-keygen -t ed25519 -a 150 -C "courriel@example.com"
```

<figure>
    <img src="content/images/blog/2023/cours/ssh-keygen.gif">
    <figcaption>
      <span class="figure__legend">Génération d'une paire de clé SSH</span>
    </figcaption>
</figure>

L'option `-C` permet d'ajouter un commentaire à votre clé, pratique notamment pour **identifier le propriétaire d'une clé publique coté serveur**.

!!! danger "Phrase de passe"
    Bien que facultative, il est « **extrêmement vachement recommandé** » de disposer d'une phrase de passe sur vos clés SSH (dans le cadre des cours et pour gagner du temps il est possible de s'en passer **si vous n'utilisez pas votre clé en dehors de ceux-ci**).

Cette commande vous aura généré deux fichiers dans le répertoire `~/.ssh/` (sauf si vous l'avez modifié bien évidemment):

- `id_ed25519.pub` (comme son extension l'indique c'est votre clé **publique**);
- `id_ed25519` votre clé **privée** (on remarquera les droits qui lui sont appliqués `0600`, en effet seul votre utilisateur doit y avoir accès).

!!! info "Générer une clé RSA"
    Ed25519 n'étant de temps en temps pas supporté (surtout par les anciens systèmes) il est parfois nécessaire de générer une paire de clé RSA (on remarquera la longueur de clé de **4096 bits** recommandée à date de rédaction de l'article):
    `ssh-keygen -t rsa -a 150 -b 4096`

## Se connecter à un serveur distant

C'est un peu la finalité.
Imaginons un serveur pour lequel votre clé est autorisée à se connecter (pour rappel fichier `authorized_keys`), nous pouvons initier une connexion à l'aide de la commande:

`ssh user@server_address`

Cette commande aura donc pour effet « d'ouvrir » une connexion sur un serveur distant via le protocol SSH vous permettant de saisir des lignes de commande **directement** sur ce serveur et donc de **l'administrer**.

<figure>
    <img src="content/images/blog/2023/cours/ssh.gif">
    <figcaption>
      <span class="figure__legend">Ouverture d'une session sur un serveur distant</span>
    </figcaption>
</figure>

Cette exemple montre l'ouverture d'une session avec l'utilisateur `debian` sur le serveur ayant pour adresse IP `146.59.243.95`.

**Plusieurs choses à retenir à cette étape:**

- Par défaut ssh **parcourt les clés SSH privées disponibles** dans le répertoire `~/.ssh` afin de les proposer au serveur auquel vous essayez de vous connecter.
- Vous optenez en retour **la première fois** que vous vous connectez un message vous demandant de **confirmer la connexion** vers le serveur distant (Host key checking).

## Compléments

Si vous disposez de plusieurs clés SSH et que vous ne souhaitez pas que l'ensemble de vos clés privées soient soumises au serveur distant vous pouvez spécifier quelle clé utiliser en utilisant l'option `-i`.

```
ssh -i ~/.ssh/id25519 debian@146.59.243.95
```

Il est possible d'utiliser **des syntaxes différentes** en fonction de votre [fichier de configuration SSH](/blog/cours/utiliser-la-configuration-ssh-client).

Vous pouvez ainsi agir sur les comportements par défaut de votre client SSH et notamment sur la clé à utiliser en fonction de tel ou tel serveur.


## Aller plus loin avec les sources

- https://fr.wikipedia.org/wiki/Secure_Shell