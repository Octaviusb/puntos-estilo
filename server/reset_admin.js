const mysql = require('mysql2');
const bcrypt = require('bcrypt');

const connection = mysql.createConnection({
  host: 'localhost',
  user: 'root',
  password: 'Obc19447/*',
  database: 'mi_proyecto'
});

// Nueva contraseña para el administrador
const adminEmail = 'obuitragocamelo@yahoo.es';
const newPassword = 'Admin123!';

// Generar hash de la contraseña
bcrypt.hash(newPassword, 10, (err, hash) => {
  if (err) {
    console.error('Error generando hash:', err);
    return;
  }

  // Actualizar la contraseña
  const sql = 'UPDATE usuarios SET contraseña = ? WHERE correo = ?';
  connection.query(sql, [hash, adminEmail], (err, result) => {
    if (err) {
      console.error('Error actualizando contraseña:', err);
      return;
    }

    if (result.affectedRows > 0) {
      console.log('✓ Contraseña actualizada exitosamente');
      console.log('Nuevas credenciales:');
      console.log('Email:', adminEmail);
      console.log('Contraseña:', newPassword);
    } else {
      console.log('No se encontró el usuario administrador');
    }

    connection.end();
  });
}); 