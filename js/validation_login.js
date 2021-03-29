$(document).ready(function()
{		
	$("#form_login").validate(
	{
        rules:{
		'username':{
			required: true,
			},
		'pass':{
			required: true
			}
		},
        messages:{
		'username':{
			required: "Il campo username è obbligatorio!"
			},
		'pass':{
			required: "Il campo password è obbligatorio!"
			}
		}
	});
});