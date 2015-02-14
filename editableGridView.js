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
        this.model=this.data('model');
        var $table = this;
        var $fields = this.find('td.editable select,td.editable input');
        $fields.on('focusout',function(e){
            if($(this).val()===$(this).parent().children('span').html()){
                $(this).removeClass('show');
            }
            });
        $fields.on('change.gridView',function(e){
                var dataSend={};
                dataSend[this.name]=$(this).val();
                dataSend.id=$(this).data('id');
                dataSend.model=$table.model;
                $table.sendAjax(this,dataSend);
            });


        this.ajaxUrl=args.url;
        this.sendAjax = function(el,data){
            $.post(this.ajaxUrl,data,function(json){
                console.log(json,typeof json);
               if(typeof json=='string'){
                   json=$.parseJSON(json);
               }
                if(json.status===1){
                    $(el).removeClass('show');
                    $(el).siblings('span').html($(el).val());
                }
            });
        };
        this.$cells=this.find('tbody td.editable');
        this.$cells.on('click', function(e){
            $table.currentCell = $(this);
            $table.currentCell.children('input,select').addClass('show').focus();
    });
    }
})(jQuery);


