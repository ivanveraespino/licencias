CREATE TABLE departamentos (
    id CHAR(2) PRIMARY KEY,
    nombre VARCHAR(50) NOT NULL
);
-- Estructura sugerida
CREATE TABLE provincias (
    id CHAR(4) PRIMARY KEY,
    nombre VARCHAR(50) NOT NULL,
    iddep CHAR(2) NOT NULL,
    FOREIGN KEY (iddep) REFERENCES departamentos(id)
);

CREATE TABLE distritos (
    id CHAR(6) PRIMARY KEY, -- Ubigeo completo (ej: 150101)
    nombre VARCHAR(100) NOT NULL,
    idprov CHAR(4) NOT NULL,
    FOREIGN KEY (idprov) REFERENCES provincias(id)
);
go
CREATE TABLE tipovia(
    id int primary key identity(1,1),
    nombre varchar(100)
)
go
create table giro (
	id int primary key identity(1,1),
	actividad varchar(250),
	codigo varchar(5),
);
go
create table tipo(
	id int primary key identity(1,1),
	denominacion varchar(250),
	idgiro int,
	foreign key (idgiro) references giro(id),
);
go
create table negocio(
	id int primary key identity(1,1),
	ruc varchar(11),
    razonsocial varchar(250),
	urlficha varchar(250),
	idgiro int,
	estado int,
);
go
create table representante(
	id int primary key identity(1,1),
	dni varchar(8),
	nombres varchar(100),
	paterno varchar(100),
	materno varchar(100),
	estado int,
    tipovia varchar(100),
	direccion varchar(250),
    celular varchar(12),
	iddis CHAR(6),
	idprov CHAR(4),
	iddep CHAR(2),
	idnegocio int,
	foreign key (iddep) references departamentos(id),
	foreign key (idprov) references provincias(id),
	foreign key (iddis) references distritos(id),
	foreign key (idnegocio) references negocio(id)
);
go

create table sede(
	id int primary key identity(1,1),
	nombrenegocio varchar(250),
	tipovia varchar(100),
	direccion varchar(250),
	tipodomicilio int,--puede ser principal(domicilio fiscal) o secundario(domicilio comercial)
	urldefensacivil varchar(250),
	urlcompatibilidadsuelos varchar(250),
	estado int,
	idtipo int,--tipo de negocio
	iddis CHAR(6),
	idprov CHAR(4),
	iddep CHAR(2),
	idnegocio int,
	foreign key (iddep) references departamentos(id),
	foreign key (idprov) references provincias(id),
	foreign key (iddis) references distritos(id),
	foreign key (idnegocio) references negocio(id)
);
go
create table licencia(
	id int primary key identity(1,1),
	tipo int,
    resolucion varchar(50),
	dias int,
	inicio date,
	fin date,
	urllicencia varchar(250),
	estado int,
	idsede int,
	foreign key (idsede) references sede(id),
	
);
go
create table horario(
	id int primary key identity(1,1),
	inicio int,
	fin int,
	idsede int,
	foreign key (idsede) references sede(id),
);
