const mysql = require('mysql');

const conexao = mysql.createConnection({
  host: 'localhost',
  user: 'root',
  password: '',
  database: 'dion_store'
});

conexao.connect((erro  ) => {
  if (erro) {
    console.error('Erro ao conectar: ' + erro.stack);
    return;
  }
  console.log('Conectado com sucesso!');
});