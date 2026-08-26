</main>

<footer class="site">
  <div class="shell footgrid">
    <div>
      <div class="fname">Dataforeningen</div>
      <p>Sammen utvikler vi den digitale fremtiden. Den norske dataforening er Norges største
      nettverk for IT- og digitaliseringsprofesjonelle. Siden 1953.</p>
    </div>
    <div>
      <h4>Ressursportalen</h4>
      <ul>
        <li><a href="<?php echo esc_url(home_url('/')); ?>">Verktøykassen</a></li>
        <li><a href="<?php echo esc_url(dnd_tips_url()); ?>">Bidra tilbake</a></li>
        <li><a href="<?php echo esc_url(dnd_side_url('om-piloten')); ?>">Om piloten</a></li>
      </ul>
    </div>
    <div>
      <h4>Snarveier</h4>
      <ul>
        <li><a href="<?php echo esc_url(dnd_side_url('mitt-medlemskap')); ?>">Mitt medlemskap</a></li>
        <li><a href="<?php echo esc_url(dnd_side_url('arrangementer')); ?>">Arrangementer</a></li>
        <li><a href="<?php echo esc_url(dnd_side_url('faggrupper')); ?>">Faggrupper</a></li>
      </ul>
    </div>
    <div>
      <h4>Kontakt</h4>
      <ul>
        <li><a href="<?php echo esc_url(dnd_tips_url()); ?>">Har du tips til innhold?</a></li>
      </ul>
    </div>
  </div>
  <div class="shell footnote">
    Pilotmiljø for Dataforeningens ressursportal (kravspec v1.2, bygges internt — beslutningslogg B10).
    Ikke et offisielt nettsted: innhold er plassholdere, innlogging er lokal i påvente av
    StyreWeb-avklaringen, og dokumenter streames fra en mock-proxy i stedet for SharePoint/Graph.
  </div>
</footer>

<?php wp_footer(); ?>
</body>
</html>
