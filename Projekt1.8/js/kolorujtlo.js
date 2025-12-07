

function changeBackground(color) {
      document.body.style.backgroundColor = color;
    }
    document.querySelectorAll('#colorButtons button').forEach(button => {
      button.addEventListener('click', () => {
        changeBackground(button.dataset.color);
      });
    });
    