$(function() {
   $('#upload_file').submit(function(e) {
      e.preventDefault();
      $.ajaxFileUpload({
         url         :'./upload/upload_file/', 
         secureuri      :false,
         fileElementId  :'userfile',
         dataType    : 'json',
         data        : {
            'title'           : $('#title').val()
         },
         success  : function (data, status)
         {
            if(data.status != 'error')
            {
               $('#files').html('<p>Reloading files...</p>');
               refresh_files();
               $('#title').val('');
            }
            alert(data.msg);
         }
      });
      return false;
   });
});