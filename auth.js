// Gerenciamento de autenticação
document.addEventListener('DOMContentLoaded', function() {
    // Verificar token na página de redefinir senha
    if (window.location.pathname.includes('redefinir_senha.html')) {
        const urlParams = new URLSearchParams(window.location.search);
        const token = urlParams.get('token');
        
        if (token) {
            document.getElementById('token').value = token;
        } else {
            mostrarMensagem('Token inválido!', 'error');
            setTimeout(() => {
                window.location.href = 'login.html';
            }, 3000);
        }
    }
    
    // Configurar formulários
    const loginForm = document.getElementById('login-form');
    const cadastroForm = document.getElementById('cadastro-form');
    const recuperacaoForm = document.getElementById('recuperacao-form');
    const redefinirForm = document.getElementById('redefinir-form');
    
    if (loginForm) {
        loginForm.addEventListener('submit', fazerLogin);
    }
    
    if (cadastroForm) {
        cadastroForm.addEventListener('submit', fazerCadastro);
    }
    
    if (recuperacaoForm) {
        recuperacaoForm.addEventListener('submit', solicitarRecuperacao);
    }
    
    if (redefinirForm) {
        redefinirForm.addEventListener('submit', redefinirSenha);
    }
});

async function fazerLogin(e) {
    e.preventDefault();
    
    const formData = new FormData(e.target);
    formData.append('acao', 'login');
    
    try {
        const response = await fetch('auth.php', {
            method: 'POST',
            body: formData
        });
        
        const resultado = await response.json();
        
        if (resultado.success) {
            mostrarMensagem(resultado.message, 'success');
            
            // Redirecionar baseado no nível do usuário
            setTimeout(() => {
                if (resultado.nivel === 'admin') {
                    window.location.href = 'dashboard.php';
                } else {
                    window.location.href = 'index.html';
                }
            }, 1500);
        } else {
            mostrarMensagem(resultado.message, 'error');
        }
    } catch (error) {
        mostrarMensagem('Erro de conexão!', 'error');
    }
}

async function fazerCadastro(e) {
    e.preventDefault();
    
    const senha = document.getElementById('senha').value;
    const confirmarSenha = document.getElementById('confirmar_senha').value;
    
    if (senha !== confirmarSenha) {
        mostrarMensagem('As senhas não coincidem!', 'error');
        return;
    }
    
    if (senha.length < 6) {
        mostrarMensagem('A senha deve ter pelo menos 6 caracteres!', 'error');
        return;
    }
    
    const formData = new FormData(e.target);
    formData.append('acao', 'cadastro');
    
    try {
        const response = await fetch('auth.php', {
            method: 'POST',
            body: formData
        });
        
        const resultado = await response.json();
        
        if (resultado.success) {
            mostrarMensagem(resultado.message, 'success');
            setTimeout(() => {
                window.location.href = 'login.html';
            }, 2000);
        } else {
            mostrarMensagem(resultado.message, 'error');
        }
    } catch (error) {
        mostrarMensagem('Erro de conexão!', 'error');
    }
}

async function solicitarRecuperacao(e) {
    e.preventDefault();
    
    const formData = new FormData(e.target);
    formData.append('acao', 'solicitar_recuperacao');
    
    try {
        const response = await fetch('auth.php', {
            method: 'POST',
            body: formData
        });
        
        const resultado = await response.json();
        
        if (resultado.success) {
            mostrarMensagem(resultado.message + ' (Debug: ' + resultado.debug_link + ')', 'success');
        } else {
            mostrarMensagem(resultado.message, 'error');
        }
    } catch (error) {
        mostrarMensagem('Erro de conexão!', 'error');
    }
}

async function redefinirSenha(e) {
    e.preventDefault();
    
    const senha = document.getElementById('senha').value;
    const confirmarSenha = document.getElementById('confirmar_senha').value;
    
    if (senha !== confirmarSenha) {
        mostrarMensagem('As senhas não coincidem!', 'error');
        return;
    }
    
    if (senha.length < 6) {
        mostrarMensagem('A senha deve ter pelo menos 6 caracteres!', 'error');
        return;
    }
    
    const formData = new FormData(e.target);
    formData.append('acao', 'redefinir_senha');
    
    try {
        const response = await fetch('auth.php', {
            method: 'POST',
            body: formData
        });
        
        const resultado = await response.json();
        
        if (resultado.success) {
            mostrarMensagem(resultado.message, 'success');
            setTimeout(() => {
                window.location.href = 'login.html';
            }, 2000);
        } else {
            mostrarMensagem(resultado.message, 'error');
        }
    } catch (error) {
        mostrarMensagem('Erro de conexão!', 'error');
    }
}

function mostrarMensagem(mensagem, tipo) {
    const elemento = document.getElementById('mensagem');
    elemento.textContent = mensagem;
    elemento.className = `mensagem ${tipo}`;
    elemento.style.display = 'block';
    
    setTimeout(() => {
        elemento.style.display = 'none';
    }, 5000);
}

// Verificar se usuário está logado
function verificarAutenticacao() {
    // Esta função pode ser usada em páginas que requerem login
    return true; // Implementar verificação de sessão
}