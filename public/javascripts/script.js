(function () {
  // Cookie consent
  window.cookieconsent.initialise({
    palette: {
      popup: {
        background: '#24333e',
        text: '#ffffff',
      },
      button: {
        background: '#e53935',
        text: '#ffffff',
      },
    },
    showLink: false,
    theme: 'classic',
    content: {
      message:
        'This website uses cookies for user authentication, we do not use third-party trackers.',
    },
  });

  // Convert UTC time to local time in time elements
  document.querySelectorAll('time.datetime').forEach((elem) => {
    let value = elem.textContent;
    let date = new Date(value);
    elem.innerHTML = date.toLocaleString();
  });

  // Convert UTC time to local time in datetime elements
  document.querySelectorAll('input.datetime').forEach((elem) => {
    let value = elem.getAttribute('value');
    let date = new Date(value);
    let year = date.getFullYear();
    let month = ('0' + date.getMonth()).slice(-2);
    let day = ('0' + date.getDate()).slice(-2);
    let hours = ('0' + date.getHours()).slice(-2);
    let minutes = ('0' + date.getMinutes()).slice(-2);
    elem.setAttribute('value', `${year}-${month}-${day}T${hours}:${minutes}`);
  });
})();
