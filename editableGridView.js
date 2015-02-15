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
        this.errorModal=$('#editable-grid-modal .msg-zone');
        $('#editable-grid-modal').children('.close').click(function(){
            $(this).parent().removeClass('show');
        });
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
                else if(json.status===0){
                    var msg,title = '';
                    $.each(json.message,function(k,v){
                        title=$('<h4>'+k+'</h4>');
                        msg=$('<div class="error">'+v.join('\n')+'</div>');
                        $table.errorModal.empty().append(title).append(msg);
                        $table.errorModal.parent().addClass('show error');
                    });
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


