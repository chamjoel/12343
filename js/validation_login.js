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
			required: "Il campo username � obbligatorio!"
			},
		'pass':{
			required: "Il campo password � obbligatorio!"
			}
		}
	});
});