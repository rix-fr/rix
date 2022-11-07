---
type:               "post"
title:              "Intégrer des agendas iCloud à Gnome Calendar"
date:               "2022-10-25"
lastModified:       ~
tableOfContent:     false
description:        "Intégrer (relativement) facilement des agendas iCloud à Gnome Calendar"

thumbnail:          "content/images/blog/thumbnails/calendar.jpg"
credits:            { name: "Estée Janssens", url: "https://unsplash.com/@esteejanssens" }
tags:               ["linux", "icloud", "calendar", "gnome"]
categories:         ["linux"]
authors:            ["gfaivre"]
---

Contrairement à ce que certains pensent on peut être utilisateur d'un iPhone (ou d'un iPad) sans avoir de mac.

Et oui c'est mon cas d'ailleurs ;)
Autant je ne changerais pas mon iPhone contre un Android (même si j'ai aussi un XPeria avec SailfishOS) autant je n'échangerais ~~pas~~ plus mon Linux contre un OSX.

Oui mais… bien qu'il devrait être très facile de synchroniser ses **agendas iCloud** avec **Gnome** (étant donné qu'iCloud propose un format [CalDAV](https://fr.wikipedia.org/wiki/CalDAV)), la réalité n'est pas tout aussi simple !

Petit guide pour épargner les arrachages de cheveux pour y parvenir.

## Les pré-requis

- Un compte iCloud, et oui…
- Avoir créé un mot de passe d'application spécifique à votre client à partir de votre AppleID (C'est tout bien expliqué [ici](https://support.apple.com/fr-fr/HT204397))
- Installer Evolution sur votre machine (pas de panique c'est temporaire &#128521;).

## Configurer vos agendas dans Evolution

<div class="side-image">
  <div class="side-image__content">
    <p>En tout premier lieu, ouvrez l'onglet « Agenda » (vous serez pas défaut sur la vue « courriel ») une fois sur la partie agenda, créer en un nouveau comme ci-contre.</p>
  </div>
  <figure>
      <img src="/content/images/blog/2022/icloud-sync-with-gnome-calendar/evolution-new-calendar.jpg" alt="Créer un nouvel agenda dans Evolution.">
  </figure>
</div>

<div class="side-image">
  <img src="/content/images/blog/2022/icloud-sync-with-gnome-calendar/evolution-connexion-icloud.jpg" alt="Informations de connexion à iCloud.">
  <div class="side-image__content">
    <p><strong>Renseignez ensuite vos informations et suivez les étapes ci-contre.</strong></p>
    <p>
      <ul>
        <li>Sélectionnez le type d'agenda: CalDAV;</li>
        <li>URL: <strong>https://caldav.icloud.com</strong> (Elle sera par la suite remplacée par l'adresse de l'agenda sélectionné);</li>
        <li>Dans le champs utilisateur, votre Apple ID (En général votre adresse de courriel);</li>
        <li>Appuyez enfin  sur « Rechercher les agendas » vous devriez avoir la liste des agendas dont vous disposez sur iCloud, sélectionnez l'agenda que vous voulez synchroniser;</li>
        <li>Vous pouvez ensuite sélectionner la couleur de votre agenda (ou conserver celle par défaut).</li>
      </ul>
    </p>
  </div>
</div>

Pour terminer, libre à vous d'activer les options afin de rendre vos agendas disponibles hors ligne et de sélectionner votre agenda par défaut.

`ATTENTION` les étapes ci-dessus devront être répétées pour `CHACUN` des agendas dont vous disposez, je n'ai pas trouvé mieux pour l'instant.
Une fois vos agendas synchronisés vous pouvez fermer Evolution, l'agenda de Gnome devrait automatiquement ajouter les agendas que vous venez de configurer.