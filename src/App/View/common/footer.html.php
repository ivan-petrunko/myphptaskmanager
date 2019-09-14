<?php
use App\Core\Html\Context;

/** @var Context $context */
?>

</div>
</main>

<footer class="footer mt-auto py-3"></footer>
<?php if (!empty($context->getJs())): ?>
    <?php foreach ($context->getJs() as $js): ?>
        <script src="<?=$js?>"></script>
    <?php endforeach; ?>
<?php endif; ?>
</body>
</html>
