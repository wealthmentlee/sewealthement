<ul class="admin_home_dashboard_links">
  <li style="width:200px">
    <ul>
      <?php foreach ($this->navigation as $item): ?>
      <li
        class="<?php echo $item->getClass(); ?> hecore-menu-tab <?php if ($item->isActive()): ?>active-menu-tab<?php endif; ?>">
        <a href="<?php echo $item->getHref() ?>">
          <?php echo $this->translate($item->getLabel()); ?>
        </a>
      </li>
      <?php endforeach; ?>
    </ul>
  </li>
</ul>
