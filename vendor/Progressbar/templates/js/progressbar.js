/**
 * Created by WRheeder on 02/02/2017.
 */
jQuery.each({
    pb : function(field, other_field, options){
        $(field).progressbar();
        //progressTimer[field] = setTimeout(progress(field), 2000 );
    },
    updateProgress : function(field,a){
        progress(field,a);
    }
}, jQuery.univ._import);

function progress(field,a){
    $(field).progressbar("value",a);
}