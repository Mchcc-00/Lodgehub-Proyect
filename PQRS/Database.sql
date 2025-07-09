CREATE DATABASE IF NOT EXISTS formulario_pqrs;

USE formulario_pqrs;

-- Crear tabla pqrs
CREATE TABLE IF NOT EXISTS pqrs (
  id INT PRIMARY KEY,
  fecha DATE NOT NULL,
  tipo_pqrs VARCHAR(50) NOT NULL,
  urgencia VARCHAR(20) NOT NULL,
  categoria VARCHAR(50) NOT NULL,
  descripcion TEXT NOT NULL,
  nombre VARCHAR(100) NOT NULL,
  apellido VARCHAR(100) NOT NULL,
  empleado VARCHAR(100) NOT NULL,
  tipo_documento VARCHAR(10) NOT NULL,
  numero_documento VARCHAR(30) NOT NULL
);
