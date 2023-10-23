/**
 * Loader Element Creation
 */
const loader = document.createElement('div')
loader.setAttribute("id", "preloader")
loader.innerHTML = `<div class="lds-ring"><div></div><div></div><div></div><div></div></div>`


/** Show Loader to document */
window.start_loader = function(){
    if(document.querySelector('#preloader') != null)
        document.querySelector('#preloader').remove()

    document.body.appendChild(loader)
}

/** Remove Loader From Document */
window.end_loader = function(){
    if(document.querySelector('#preloader') != null)
        document.querySelector('#preloader').remove()
}
/** Show Loader before unloading the document */
window.addEventListener('beforeunload', async function(e){
    e.preventDefault()
    start_loader()
    
})
$(function() {
    $('#expenseModal').on('hide.bs.modal', e =>{
        var _form = $('#expenseModal form')
        _form[0].reset()
    })
})