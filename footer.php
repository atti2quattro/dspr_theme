<?php
/**
 * Il template per il footer
 *
 * @package dspr
 */
?>

<footer class="dib-footer">
  <div class="dib-footer__grid">

    <!-- Colonna sinistra: logo + dati legali -->
    <div>
      <div style="margin-bottom:12px;">
        <?php if ( has_custom_logo() ) :
          the_custom_logo();
        else : ?>
          <a href="<?php echo esc_url( home_url( '/' ) ); ?>" rel="home" style="display:block;">
            <img src="https://www.ildispariquotidiano.it/it/wp-content/uploads/2026/03/BR_DEF.png"
                 alt="<?php bloginfo( 'name' ); ?>"
                 style="height:74px;width:auto;display:block;">
          </a>
        <?php endif; ?>
      </div>
      <div class="dib-footer__info">
        Iscr. Trib. NA. al n. 19 del 21.04.2015<br>
        Editoriale Ischia srl<br>
        Via Michele Mazzella, 202<br>
        80077 Ischia (Na)<br><br>
        Direttore responsabile:<br>
        Gaetano Di Meglio
      </div>
    </div>

    <!-- Colonna destra: contatti + partner + note -->
    <div>
      <div class="dib-footer__contacts" style="margin-bottom:16px;">
        <a href="tel:08118909067" class="dib-footer__contact">
          <span class="dib-footer__contact-icon">☎</span>
          081 18909067
        </a>
        <a href="mailto:redazione@ildispari.it" class="dib-footer__contact">
          <span class="dib-footer__contact-icon">✉</span>
          redazione@ildispari.it
        </a>
      </div>

      <div class="dib-footer__right-label">Informazione nazionale<br>in collaborazione con</div>

      <div class="dib-footer__partners">
        <a href="https://www.italpress.com" target="_blank" rel="noopener noreferrer" class="dib-footer__partner">
          <span class="dib-footer__partner-dot"></span>
          Ital Press
        </a>
        <a href="https://www.adnkronos.com" target="_blank" rel="noopener noreferrer" class="dib-footer__partner">
          <span class="dib-footer__partner-dot"></span>
          Adnkronos
        </a>
      </div>
		<div class="dib-footer__right-label" style="margin-top:16px;">In rassegna stampa con</div>
      <div class="dib-footer__partners">
        <a href="https://www.datastampa.it" target="_blank" rel="noopener noreferrer" class="dib-footer__partner">
          <span class="dib-footer__partner-dot"></span>
          Data Stampa
        </a>
        <a href="https://www.ecodellastampa.it" target="_blank" rel="noopener noreferrer" class="dib-footer__partner">
          <span class="dib-footer__partner-dot"></span>
          Eco della Stampa
        </a>
        <a href="https://www.sifa.it" target="_blank" rel="noopener noreferrer" class="dib-footer__partner">
          <span class="dib-footer__partner-dot"></span>
          Sifa
        </a>
      </div>

      <div class="dib-footer__stock">
        Stock images by <a href="https://depositphotos.com/it/" target="_blank" rel="noreferrer noopener">Depositphotos</a><br>
        <a href="<?php echo esc_url( get_privacy_policy_url() ); ?>">Privacy Policy</a>
      </div>
    </div>

  </div>

  <div class="dib-footer__bottom">
    &copy; <?php echo date( 'Y' ); ?> Il Dispari Quotidiano &mdash; Tutti i diritti riservati
  </div>
</footer>

<?php wp_footer(); ?>
</body>
</html>
