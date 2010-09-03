function confirmSubmit() {
    var answer = confirm("Are you sure you want to submit?")
    if (answer){
        return true;
    }
    else{
        return false;
    }
}



function populateSubscriptions(id){
	
	$.getJSON("getSubscriptions.php",{certificateId: $(id).val(), ajax: 'true'}, function(j){
      var options = '';
      options += '<option value="0">Any</option>';
      
      for (var i = 0; i < j.length; i++) {
        options += '<option value="' + j[i].AppSubscriptionId + '">' + j[i].SubscriptionName + '</option>';
      }
      
      $("#subscriptionId").html(options);
    
    })

}

function certificateOnSelectChange(){
		
	populateSubscriptions(this);	
	
}