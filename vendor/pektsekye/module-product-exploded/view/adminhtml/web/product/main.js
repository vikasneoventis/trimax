
require([
    "jquery",   
    "jquery/ui"   
],function($, mageTemplate) {

  "use strict";
  $.widget("pektsekye.productExploded", {
  		
    lastId : 0,
    lastHintId : 0,	
    defaultAreaSize : 40,
    lastTopPosition : 10,
    
    
    _create: function(){   		    
    
      this.lastId = this.options.lastLabelId + 1;
      this.lastHintId = this.options.lastHintId; 
           
      $('#productexploded_image_container span.pe-label').draggable({
        stop: $.proxy(this.onDragStop, this)
      });
      
      this._on({ 
          'click #pe_add_label_button' : $.proxy(this.addLabel, this),
          'click button.pe-delete-label' : $.proxy(this.deleteLabel, this),
          'change .pe-area-size input' : $.proxy(this.updateAreaSize, this),
          'change input.pe-label-field-title' : $.proxy(this.updateLabelText, this),         
          'change input.pe-label-field-link-to-number' : $.proxy(this.updateLinkToNumber, this)                        
      });          
    },
  
  
    addLabel : function(){
     $('#pe_labels_table tr').last().before(
      '<tr id="pe_label_row_'+this.lastId+'">'+
      '<td class="pe-hint-id">'+this.lastHintId+'</td>'+           
      '<td class="pe-area-size">'+
      '    <input data-form-part="product_form" type="text" id="pe_label_field_'+this.lastId+'_width" name="product[pe_label][new]['+this.lastId+'][width]" value="" />'+
      '     X '+
      '    <input data-form-part="product_form" type="text" id="pe_label_field_'+this.lastId+'_height" name="product[pe_label][new]['+this.lastId+'][height]" value="" />'+
      '</td>'+
      '<td><input data-form-part="product_form" class="pe-label-field-title" type="text" id="pe_label_field_'+this.lastId+'_title" name="product[pe_label][new]['+this.lastId+'][title]" value=""/></td>'+
      '<td>'+
      '    <input data-form-part="product_form" class="pe-label-field-link-to-number" type="text" id="pe_label_field_'+this.lastId+'_link_to_number" name="product[pe_label][new]['+this.lastId+'][link_to_number]" value=""/>'+
      '    <input data-form-part="product_form" type="hidden" id="pe_label_field_'+this.lastId+'_x" name="product[pe_label][new]['+this.lastId+'][x]" value="" />'+
      '    <input data-form-part="product_form" type="hidden" id="pe_label_field_'+this.lastId+'_y" name="product[pe_label][new]['+this.lastId+'][y]" value="" />'+
      '</td>'+
      '<td><button class="action-secondary pe-delete-label" title="'+this.options.deleteButtonText+'" type="button"><span data-bind="text: title">X</span></button></td>'+      
      '</tr>'      
     );  
     
     $('#productexploded_image_container').append(
      '<span id="pe_label_'+this.lastId+'" class="pe-label pe-label-area"><span class="pe-label-hint">'+this.lastHintId+'</span><span class="pe-text"></span></span>'
     );
     
     var tr = $('#pe_label_row_'+this.lastId);     
     
     var width = this.defaultAreaSize;
     var height = this.defaultAreaSize;
         
     var previousTr = tr.prev('tr[id^="pe_label_row_"]');
     if (previousTr.length){
      var prevWidth = parseInt(previousTr.find('input[name$="[width]"]').val());
      if (!isNaN(prevWidth) && prevWidth > 0)
        width = prevWidth;
      var prevHeight = parseInt(previousTr.find('input[name$="[height]"]').val()); 
      if (!isNaN(prevHeight) && prevHeight > 0)
        height = prevHeight;           
     }
     
     $('#pe_label_field_'+this.lastId+'_width').val(width);
     $('#pe_label_field_'+this.lastId+'_height').val(height);
        
     tr.find('input[name$="[link_to_number]"]').focus(); 
     
     var positionLeft = 10;
     var positionTop = this.lastTopPosition;
     
     $('#pe_label_'+this.lastId).css({
      'width': width+'px',
      'height': height+'px',
      'left': positionLeft+'px',
      'top': positionTop+'px'
      }).draggable({
        stop: $.proxy(this.onDragStop, this)
      }); 
      
     $('#pe_label_field_'+this.lastId+'_x').val(positionLeft);
     $('#pe_label_field_'+this.lastId+'_y').val(positionTop);      
      
     this.lastTopPosition = positionTop + height + 5;
     this.lastId++;
     this.lastHintId++;                      
    },  
   
    
    deleteLabel : function(e){ 
     var tr = $(e.target).closest("tr");
     var labelId = tr[0].id.match(/\d+/)[0];
     var oldInputs = tr.find("input[name^='product[pe_label][update]']");
     if (oldInputs.length){
       tr.find('input').last().after('<input data-form-part="product_form" type="hidden" name="product[pe_label][delete]['+labelId+']" value="'+labelId+'"/>');
       tr.hide();    
     } else {
       tr.remove();        
     }  
     $('#pe_label_'+labelId).remove();    
    },
  
    onDragStop : function(event, ui){
      var labelId = ui.helper[0].id.match(/\d+/)[0];
      $('#pe_label_field_'+labelId+'_x').val(ui.position.left);
      $('#pe_label_field_'+labelId+'_y').val(ui.position.top);      
    },

    updateAreaSize : function(e){
      var id = $(e.target)[0].id;
      var labelId = id.match(/\d+/)[0];
      var label = $('#pe_label_'+labelId);
      var size = this.getLabelWidthHeight(labelId);    
      label.css({
        'width': size.width+'px',
        'height': size.height+'px'
      })       
    },  
    
    updateLabelText : function(e){
      var labelId = $(e.target)[0].id.match(/\d+/)[0];
      var label = $('#pe_label_'+labelId);
      var title = $('#pe_label_field_'+labelId+'_title').val();
      if (title){
        if (label.hasClass('pe-label-area')){
          label.toggleClass('pe-label-area pe-label-text');
          label.css({
            'width': 'auto',
            'height': ''
          })              
        }   
      } else {
        label.toggleClass('pe-label-text pe-label-area');
        var size = this.getLabelWidthHeight(labelId);    
        label.css({
          'width': size.width+'px',
          'height': size.height+'px'
        })                  
      }
      label.find('.pe-text').text(title);
    },
    
    updateLinkToNumber : function(e){
      var labelId = $(e.target)[0].id.match(/\d+/)[0];
      var label = $('#pe_label_'+labelId);
      var number = $('#pe_label_field_'+labelId+'_link_to_number').val();
      label[0].title = number;            
    },
    
    getLabelWidthHeight : function(labelId){
      var widthEl = $('#pe_label_field_'+labelId+'_width');
      var heightEl = $('#pe_label_field_'+labelId+'_height');            
      var width = widthEl.val();
      var height = heightEl.val();
      
      if (width > 100 && width > this.options.imageWidth){
        width = this.options.imageWidth;
        widthEl.val(width);
      }
      if (height > 100 && height > this.options.imageHeight){
        height = this.options.imageHeight;        
        heightEl.val(height);
      }
      return {width:width, height:height};    
    }    
    
    
  });
  
});