import './styles/main-menu.css';

const loadButton = document.getElementById('loadGame');
const gameId = document.getElementById('gameId');

const loadGameById = () => {
    window.location.href = '/chat/' + gameId.value;
};

loadButton.addEventListener('click', loadGameById);
