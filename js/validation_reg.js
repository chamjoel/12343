$(document).ready(function()
{
	// my method for validate username
	$.validator.addMethod("username_regex", function(value, element) { 
		return this.optional(element) || /^[a-z0-9\.\-_]{3,30}$/i.test(value); 
		}, "Please choise a username with only a-z 0-9.");
		
	$("#form_register").validate(
	{
        rules:{
		'username':{
			required: true,
			minlength: 3,
			username_regex: true,
			remote:{
				url: "ajax/validatorAJAX_reg.php",
				type: "post"
				}
			},
		'email':{
			required: true,
			email: true,
			remote:{
				url: "ajax/validatorAJAX_reg.php",
				type: "post"
				}
			},
		'pass1':{
			required: true,
			minlength: 8
			},
		'pass2':{
			equalTo: '#reg_pass1'
			}
		},
        messages:{
		'username':{
			required: "Il campo username è obbligatorio!",
			minlength: "Scegli un username di almeno 4 lettere!",
			username_regex: "Hai utilizzato caratteri non validi. Sono consentiti solo lettere numeri!",
			remote: "L'username è già utilizzato da un altro utente!"
			},
		'email':{
			required: "Il campo email è obbligatorio!",
			email: "Inserisci un valido indirizzo email!",
			remote: "Esiste già una registrazione per questo indirizo email! Esegui la procedura di smarrimento password!"
			},
		'pass1':{
			required: "Il campo password è obbligatorio!",
			minlength: "Inserisci una password di almeno 8 caratteri!"
			},
		'pass2':{
			equalTo: "Le due password non coincidono!"
			}
		}
	});
});
