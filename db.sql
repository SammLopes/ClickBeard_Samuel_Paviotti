-- Migration: create_users_table
CREATE TABLE users (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    role ENUM('client', 'admin') DEFAULT 'client',
   
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Migration: create_barbers_table
CREATE TABLE barbers (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255) UNIQUE NOT NULL,
    age INT UNSIGNED,
    phone VARCHAR(20),
    hire_date DATE NOT NULL,
    is_active BOOLEAN DEFAULT TRUE,

    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Migration: create_specialties_table
CREATE TABLE specialties (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) UNIQUE NOT NULL,
    description TEXT,
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Migration: create_barber_specialty_table (tabela pivot)
CREATE TABLE barber_specialty (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    barber_id BIGINT UNSIGNED NOT NULL,
    specialty_id BIGINT UNSIGNED NOT NULL,
    experience_years INT DEFAULT 0,
    is_primary BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  
    FOREIGN KEY (barber_id) REFERENCES barbers(id) ON DELETE CASCADE,
    FOREIGN KEY (specialty_id) REFERENCES specialties(id) ON DELETE RESTRICT,
    UNIQUE KEY unique_barber_specialty (barber_id, specialty_id)
);

-- Migration: create_services_table
CREATE TABLE services (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    description TEXT,
    specialty_id BIGINT UNSIGNED NULL
    price DECIMAL(8,2) NOT NULL,
    duration_minutes INT NOT NULL,
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    FOREIGN KEY (specialty_id) REFERENCES specialties(id) ON DELETE SET NULL
);

-- Migration: create_scheduling_table
CREATE TABLE scheduling (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id BIGINT UNSIGNED NOT NULL,
    barber_id BIGINT UNSIGNED NOT NULL,
    service_id BIGINT UNSIGNED NOT NULL,
    
    scheduling_date DATE NOT NULL,
    scheduling_time TIME NOT NULL,
    status ENUM('scheduled', 'confirmed', 'completed', 'cancelled') DEFAULT 'scheduled',
    notes TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE RESTRICT,
    FOREIGN KEY (barber_id) REFERENCES barbers(id) ON DELETE RESTRICT,
    FOREIGN KEY (service_id) REFERENCES services(id) ON DELETE RESTRICT,
    UNIQUE KEY unique_scheduling (barber_id, scheduling_date, scheduling_time)
);

-- Inserir dados iniciais
INSERT INTO barbers (name, email, phone, specialties) VALUES
('João Silva', 'joao@barbearia.com', '11999999999', 'Corte masculino, Barba, Bigode'),
('Pedro Santos', 'pedro@barbearia.com', '11888888888', 'Corte infantil, Corte feminino, Escova'),
('Carlos Oliveira', 'carlos@barbearia.com', '11777777777', 'Corte degradê, Barba, Sobrancelha');

INSERT INTO users (name, email, password, role) VALUES
('Admin', 'admin@barbearia.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin');

-- Inserir dados iniciais de especialidades
INSERT INTO specialties (name, description) VALUES
('Corte Masculino', 'Especialização em cortes de cabelo masculinos tradicionais e modernos'),
('Barba e Bigode', 'Especialização em cuidados com barba, bigode e aparagem'),
('Sobrancelha', 'Especialização em design e cuidados com sobrancelhas'),
('Corte Infantil', 'Especialização em cortes para crianças'),
('Corte Feminino', 'Especialização em cortes de cabelo femininos'),
('Escova Progressiva', 'Especialização em tratamentos capilares e alisamentos'),
('Corte Degradê', 'Especialização em cortes modernos com degradê'),
('Tratamentos Capilares', 'Especialização em hidratação e tratamentos para cabelo');

INSERT INTO services (name, description, specialty_id, price, duration_minutes) VALUES
('Corte Masculino', 'Corte de cabelo masculino tradicional', 1,25.00, 30),
('Corte + Barba', 'Corte de cabelo + barba completa', 2,35.00, 45),
('Apenas Barba', 'Apenas barba e bigode', 2,15.00, 20),
('Corte Infantil', 'Corte especial para crianças', 4,20.00, 25),
('Corte Degradê', 'Corte moderno com degradê', 7,30.00, 40);

-- Relacionar barbeiros com especialidades
INSERT INTO barber_specialty (barber_id, specialty_id, experience_years, is_primary) VALUES
-- João Silva
(1, 1, 5, TRUE),  
(1, 2, 3, FALSE), 
(1, 7, 2, FALSE), 

-- Pedro Santos  
(2, 4, 8, TRUE),  
(2, 5, 6, FALSE), 
(2, 6, 4, FALSE),  

-- Carlos Oliveira
(3, 7, 10, TRUE),  
(3, 2, 8, FALSE),  
(3, 3, 5, FALSE);  