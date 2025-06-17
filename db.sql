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
    phone VARCHAR(20),
    specialties TEXT,
    is_active BOOLEAN DEFAULT TRUE,

    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Migration: create_services_table
CREATE TABLE services (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    description TEXT,
    price DECIMAL(8,2) NOT NULL,
    duration_minutes INT NOT NULL,
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
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

INSERT INTO services (name, description, price, duration_minutes) VALUES
('Corte Masculino', 'Corte de cabelo masculino tradicional', 25.00, 30),
('Corte + Barba', 'Corte de cabelo + barba completa', 35.00, 45),
('Apenas Barba', 'Apenas barba e bigode', 15.00, 20),
('Corte Infantil', 'Corte especial para crianças', 20.00, 25),
('Corte Degradê', 'Corte moderno com degradê', 30.00, 40);

INSERT INTO users (name, email, password, role) VALUES
('Admin', 'admin@barbearia.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin');