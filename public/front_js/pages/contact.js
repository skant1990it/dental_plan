/*
 *  Document   : contact.js
 *  Author     : pixelcave
 *  Description: Custom javascript code used in Contact page
 */

var Contact = function() {

    return {
        init: function() {
            

            /* Initialize Form Validation */
            $('#form-contact').validate({
                errorClass: 'help-block animation-pullUp', // You can change the animation class for a different entrance animation
                errorElement: 'div',
                errorPlacement: function(error, e) {
                    e.parents('.group').append(error);
                },
                highlight: function(e) {
                    $(e).closest('.group').removeClass('has-success has-error').addClass('has-error');
                    $(e).closest('.help-block').remove();
                },
                success: function(e) {
                    // You can use the following if you would like to highlight with green color the input after successful validation!
                    e.closest('.group').removeClass('has-success has-error'); // e.closest('.form-group').removeClass('has-success has-error').addClass('has-success');
                    e.closest('.help-block').remove();
                },
                rules: {
                    'contact-fname': {
                        required: true,
                        minlength: 3
                    },
                    'contact-lname': {
                        required: true,
                        minlength: 3

                    },
                    'contact-phone': {
                        required: true,
                        minlength: 10
                    },
                    'contact-message': {
                        required: true,
                        minlength: 15
                    },
                },
                messages: {
                    'contact-fname': {
                        required: 'Please let us know your first name!',
                        minlength: 'Please let us know your full name!'
                    },
                    'contact-lname': {
                        required: 'Please let us know your last name!',
                        minlength: 'Please let us know your full name!'
                    },
                    'contact-phone': 'Please let us know your valid phone no.!',
                    'contact-message': {
                        required: 'Please let us know how we can assist!',
                        minlength: 'Please let us know how we can assist!'
                    }
                }
            });
        }
    };
}();
