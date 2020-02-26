if (document.querySelector('[name="resolution"]').value === '') {
    setFormState();
    if (document.querySelector('[name="resolution"]').value === '') {
        document.querySelector('[name="resolution"]').value = window.screen.width + 'x' + window.screen.height;
    }
}

function setFormState() {
    document.querySelectorAll('input, select').forEach(function (item) {
        let name = item.getAttribute("name");
        if (item.getAttribute('type') === 'checkbox') {
            item.checked = localStorage.getItem(name) === 'on';
        } else {
            item.value = localStorage.getItem(name)
        }
    });
}

function saveFormState() {
    document.querySelectorAll('input, select').forEach(function (item) {
        let name = item.getAttribute("name");
        let value = item.value;
        if (item.getAttribute('type') === 'checkbox') {
            value = 'off';
            if (document.querySelectorAll('[name = "' + name + '"]:checked').length) {
                value = 'on';
            }
        }
        localStorage.setItem(name, value);
    });
    return true;
}