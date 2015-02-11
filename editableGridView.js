/**
 *
 * @param {jQuery} $
 *
 * @returns {undefined}
 */
(function($){
    /**
     *
     * @param {object} args {url: url}
     */
    $.fn.editableGridView = function(args){
        var $table = this;
        this.ajaxUrl=args.url;
        this.sendAjax = function(data){
            $.post(this.ajaxUrl,{method:'post',data:data});
        };
        this.$cells=this.find('tbody td.editable');
        this.$cells.on('dblclick', function(e){
            $table.currentCell = $(this);
            $table.currentCell.children('input,select').addClass('show');
            $table.currentCell.on('change',function(e){
                var dataSend={};
                dataSend[this.name]=$(this).val();
                dataSend.id=$(this).data('id');
                $table.sendAjax(dataSend);
            });
        });

    }
})(jQuery);


