function showSwal(type) {
    // Format the type
    let formattedType = type.split('-').map(word => word.charAt(0).toUpperCase() + word.slice(1)).join(' ');

    Swal.fire({
        title: 'Apply Now - ' + formattedType,
        text: 'You selected ' + formattedType + '. You need an account to apply, Click Okay to sign up',
        icon: 'info',
        confirmButtonText: 'Okay.'
    }).then((result) => {
        if (result.isConfirmed) {
            window.location.href = 'php/signup';
        }
    });
}

