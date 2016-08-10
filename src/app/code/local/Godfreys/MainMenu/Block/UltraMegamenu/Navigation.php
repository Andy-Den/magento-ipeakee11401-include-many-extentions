<?php
class Godfreys_MainMenu_Block_UltraMegamenu_Navigation extends Infortis_UltraMegamenu_Block_Navigation
{
    public function renderCategoriesMenuHtml2($x3c = FALSE, $x11 = 0, $x15 = '', $x16 = '')
    {
        $this->_isAccordion = $x3c;
        $this->_isWide = Mage::helper('ultramegamenu')->getCfg('mainmenu/wide_menu');
        $x3d = array();
        foreach ($this->getStoreCategories() as $x1d) {
            if ($x1d->getIsActive()) {
                $x3d[] = $x1d;
            }
        }
        $x3e = count($x3d);
        $x3f = ($x3e > 0);
        if (!$x3f) {
            return '';
        }
        $x18 = '';
        $x3b = 0;
        foreach ($x3d as $x10) {
            $x18 .= $this->_renderCategoryMenuItemHtml2($x10, $x11, ($x3b == $x3e - 1), ($x3b == 0), true, $x15, $x16, true);
            $x3b++;
        }
        return $x18;
    }

    protected function _renderCategoryMenuItemHtml2($x10, $x11 = 0, $x12 = false, $x13 = false, $x14 = false, $x15 = '', $x16 = '', $x17 = false)
    {
        if (!$x10->getIsActive()) {
            return '';
        }
        $x18 = array();
        if (Mage::helper('catalog/category_flat')->isEnabled()) {
            $x19 = (array)$x10->getChildrenNodes();
            $x1a = count($x19);
        } else {
            $x19 = $x10->getChildren();
            $x1a = $x19->count();
        }
        $x1b = ($x19 && $x1a);
        $x1c = array();
        foreach ($x19 as $x1d) {
            if ($x1d->getIsActive()) {
                $x1c[] = $x1d;
            }
        }
        $x1e = count($x1c);
        $x1f = ($x1e > 0);
        $x20 = Mage::helper('ultramegamenu');
        $x21 = Mage::getModel('catalog/category')->load($x10->getId());
        $x22 = FALSE;
        if ($this->_isWide) {
            $x22 = $x1f;
            if ($x20->getCfg('widemenu/show_if_no_children')) {
                $x22 = true;
            }
        }
        $width = $this->_getCatBlock($x21, 'umm_cat_block_width');
        $x23 = array();
        $x24 = array();
        $x25 = false;
        $x26 = 0;
        if ($x11 == 0 && $this->_isAccordion == FALSE && $x22) {
            $x27 = $this->_getCatBlock($x21, 'umm_cat_block_right');
            $x28 = 6;
            if ($x29 = $x21->getData('umm_cat_block_proportions')) {

                $x29 = explode("\x2f", $x29);
                $x2a = $x29[0];
                $x2b = $x29[1];
            } else {
                $x2a = 4;
                $x2b = 2;
            }
            $x26 = $x2a + $x2b;
            if (empty($x27)) {
                if ($x29 = $x21->getData('umm_cat_block_proportions')) {
                    $x29 = explode("/", $x29);
                    $x2a = $x29[0];
                    $x2b = 0;
                }
                $x2a += $x2b;
                $x2b = 0;
                $x2c = 'grid12-12';
            } elseif (!$x1f) {
                $x2b += $x2a;
                $x2a = 0;
                $x2d = 'grid12-12';
            } else {
                $x2e = 12 / $x26;
                $x2c = 'grid12-' . ($x2a * $x2e);
                $x2d = 'grid12-' . ($x2b * $x2e);
            }
            $x26 = $x2a + $x2b;
            $x2f = '';
            if ($x2f = $this->_getCatBlock($x21, 'umm_cat_block_top')) {
                $x23[] = '<div class="nav-block nav-block-top grid-full std">';
                $x23[] = $x2f;
                $x23[] = '</div>';
            }
            if ($x1f) {
                $x30 = 'itemgrid itemgrid-' . $x2a . 'col';
                $x23[] = '<div class="nav-block nav-block-center ' . $x2c . ' ' . $x30 . '">';
                $x24[] = '</div>';
            }
            if ($x27) {
                $x24[] = '<div class="nav-block nav-block-right std ' . $x2d . '">';
                $x24[] = $x27;
                $x24[] = '</div>';
            }
            if ($x2f = $this->_getCatBlock($x21, 'umm_cat_block_bottom')) {
                $x24[] = '<div class="nav-block nav-block-bottom grid-full std">';
                $x24[] = $x2f;
                $x24[] = '</div>';
            }
            if (!empty($x23) || !empty($x24)) $x25 = true;
        }
        $x31 = array();
        $x31[] = 'level' . $x11;
        $x31[] = 'nav-' . $this->_getItemPosition($x11);
        if ($this->isCategoryActive($x10)) {
            $x31[] = 'active';
        }
        $x32 = '';
        if ($x14 && $x15) {
            $x31[] = $x15;
            $x32 = ' class="' . $x15 . '"';
        }
        if ($x13) {
            $x31[] = 'first';
        }
        if ($x12) {
            $x31[] = 'last';
        }
        $x33 = ($x1f || $x25) ? true : false;
        if ($x33) {
            $x31[] = 'parent';
        }
        if ($x11 == 1 && $this->_isAccordion == FALSE && $this->_isWide) {
            $x31[] = 'item';
        }
        $x34 = array();
        if (count($x31) > 0) {
            $x34['class'] = implode(' ', $x31);
        }
        if ($x1f && !$x17) {
            $x34['onmouseover'] = 'toggleMenu(this,1)';
            $x34['onmouseout'] = 'toggleMenu(this,0)';
        }
        $x35 = '<li';
        foreach ($x34 as $x36 => $x37) {
            $x35 .= ' ' . $x36 . '="' . str_replace('"', '\"', $x37) . '"';
        }
        $x35 .= ($x11 == 0 && !empty($width) ? ' style="position: relative"' : '').'>';
        $x18[] = $x35;
        if ($x11 == 1 && $this->_isAccordion == FALSE && $this->_isWide) {
            if ($x2f = $this->_getCatBlock($x21, 'umm_cat_block_top')) {
                $x18[] = '<div class="nav-block nav-block-level1-top std">';
                $x18[] = $x2f;
                $x18[] = '</div>';
            }
        }
        $x38 = $this->_getCategoryLabelHtml($x21, $x11);
        $x39 = '';
        if ($x33 && $x11 == 0 && $this->_isAccordion == FALSE) {
            $x39 = '<span class="caret">&nbsp;</span>';
        }
        $x18[] = '<a href="' . $this->getCategoryUrl($x10) . '"' . $x32 . '>';
        $x18[] = '<span>' . $this->escapeHtml($x10->getName()) . $x38 . '</span>' . $x39;
        $x18[] = '</a>';
        $x3a = '';
        $x111a = '';
        $x3b = 0;
        $left = $this->_getCatBlock($x21, 'umm_cat_block_left');
        if($left){
            $x111a .= $left;
        }
        //else{
            foreach ($x1c as $x1d) {
                $x3a .= $this->_renderCategoryMenuItemHtml2($x1d, ($x11 + 1), ($x3b == $x1e - 1), ($x3b == 0), false, $x15, $x16, $x17);
                $x3b++;
            }
        //}
        if ($x11 == 0 && $this->_isAccordion == FALSE && $this->_isWide) {
            $x16 = 'level0-wrapper dropdown-' . $x26 . 'col';
        }
        if (!empty($x3a) || $x25) {
            if ($this->_isAccordion == TRUE) $x18[] = '<span class="opener">&nbsp;</span>';
            if ($x16) {
                $x18[] = '<div class="' . $x16 . '"'.(!empty($width) ? ' style="width: '.$width.'px;"' : '').'><div class="level0-wrapper2"'.(!empty($width) ? ' style="width: '.$width.'px;"' : '').'>';
            }
            $x18[] = implode("", $x23);
            if (!empty($x3a)) {
                $x18[] = '<ul class="level' . $x11 . '">';
                $x18[] = $x111a;
                if($x111a)
                $x18[] = str_replace('<li class="level1', '<li class="level1 mobile', $x3a);
                else
                    $x18[] = $x3a;
                $x18[] = '</ul>';
            }
            $x18[] = implode("", $x24);
            if ($x16) {
                $x18[] = '</div>';
            }
        }
        if ($x11 == 1 && $this->_isAccordion == FALSE && $this->_isWide) {
            if ($x2f = $this->_getCatBlock($x21, 'umm_cat_block_bottom')) {
                $x18[] = '<div class="nav-block nav-block-level1-bottom std">';
                $x18[] = $x2f;
                $x18[] = '</div>';
            }
        }
        $x18[] = '</li>';
        $x18 = implode("\n", $x18);
        return $x18;
    }
}
