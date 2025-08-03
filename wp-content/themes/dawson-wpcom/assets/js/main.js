
function add_categories_to_body(){
    const body = document.querySelector('body');
    const bookDetails = document.querySelector('.book-details');
    console.log('Book Details Element:', body);
    console.log('Book Details Element:', bookDetails);
    if (bookDetails) {
            const primaryColor = bookDetails.dataset.primaryColor;
            const secondaryColor = bookDetails.dataset.secondaryColor;
            const tertiaryColor = bookDetails.dataset.tertiaryColor;
            const textColor = bookDetails.dataset.textColor;
        console.log('Primary Color:', primaryColor);
        console.log('Secondary Color:', secondaryColor);
        console.log('Text Color:', textColor);  
        console.log('tertiaryColor:', tertiaryColor);  
        
        body.style.setProperty('--primary-color', primaryColor);
        body.style.setProperty('--secondary-color', secondaryColor);
        body.style.setProperty('--tertiary-color', tertiaryColor);
        body.style.setProperty('--text-color', textColor);
    }
}


add_categories_to_body();
