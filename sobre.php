<?php
// sobre.php - Página sobre a empresa
include './scripts/menu.php'; // Incluir o header
?>
<link rel="stylesheet" href="css/sobre.css">


<main>
    <section class="sobre">
        <div class="container">
            <h1 class="titulo">Sobre Nós</h1>
            <p class="descricao">
                Bem-vindo ao nosso serviço de aluguer de carros. Com anos de experiência, 
                oferecemos soluções de mobilidade de confiança e de alta qualidade para os nossos clientes.
            </p>

            <div class="conteudo-sobre">
                <div class="missao">
                    <h2>A Nossa Missão</h2>
                    <p>
                        Proporcionar uma experiência de aluguer de carros simples, acessível e confiável, 
                        assegurando a máxima satisfação dos nossos clientes.
                    </p>
                </div>
                <div class="missao">
                    <h2>Os Nossos Valores</h2>
                    <ul>
                        <li>Compromisso com a qualidade.</li>
                        <li>Respeito pelo cliente.</li>
                        <li>Inovação constante.</li>
                    </ul>
                </div>
            </div>

            <div class="experiencia">
                <h2>Nossa Experiência</h2>
                <p>
                    Com anos de atuação no mercado, acumulamos vasta experiência no setor de mobilidade.
                    Nossa equipe é formada por especialistas que garantem um atendimento de qualidade, 
                    oferecendo soluções que atendem às diversas necessidades dos nossos clientes.
                </p>
            </div>
        </div>
    </section>
</main>

<?php
include './scripts/footer.php'; // Incluir o footer
?>