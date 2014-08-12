/**
 * aheadWorks Co.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://ecommerce.aheadworks.com/LICENSE-M1.txt
 *
 * @category   AW
 * @package    AW_Featured
 * @copyright  Copyright (c) 2011 aheadWorks Co. (http://www.aheadworks.com)
 * @license    http://ecommerce.aheadworks.com/LICENSE-M1.txt
 */

var afpGrid = Class.create({
    initialize: function(params) {
        if(typeof params != 'undefined') {
            this.blockId = this._getValue(params.blockId);
        }
        if(this.blockId) {
            this.totalHeight = 10;
            $$('#'+this.blockId+' ul').each(function(elm) {
                this.rowHeight = 0;
                $(elm.childElements()).each(function(elem) {
                    this.rowHeight = Math.max(elem.getHeight(), this.rowHeight);
                }, this);
                this.totalHeight += this.rowHeight;
            }, this);
            if($(this.blockId).getHeight() < this.totalHeight)
                $(this.blockId).setStyle({height: this.totalHeight+'px'});
        }
    },

    _getValue: function(variable) {
        if(typeof variable == 'undefined') return null;
        return variable;
    }
});
;
