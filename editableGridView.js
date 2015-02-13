/**
 *
 * @param {jQuery} $
 *
 * @returns {jQuery}
 */
(function($){
    /**
     *
     * @param {object} args {url: url}
     */
    $.fn.editableGridView = function(args){
        this.addClass('editable-grid');
        this.find('td.editable select,td.editable input').on('focusout',function(e){
                $(this).removeClass('show');
            });
        var $table = this;
        this.ajaxUrl=args.url;
        this.sendAjax = function(data){
            $.post(this.ajaxUrl,data);
        };
        this.$cells=this.find('tbody td.editable');
        this.$cells.on('click', function(e){
            $table.currentCell = $(this);
            $table.currentCell.children('input,select').addClass('show').focus();
            $table.currentCell.children('input,select').on('change',function(e){
                var dataSend={};
                dataSend[this.name]=$(this).val();
                dataSend.id=$(this).data('id');
                $table.sendAjax(dataSend);
            });

        });
    }
})(jQuery);


