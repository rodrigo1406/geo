# geo
## Processador de tabelas com informações sobre países

Protótipo: http://biodiversus.com.br/geo/

Quando queremos cruzar informações sobre diferentes países, podemos precisar de fontes distintas. Por exemplo, dados econômicos podem vir do Banco Mundial, e dados sobre desmatamento podem vir de um artigo científico. Podemos encontrar dados culturais em um site, e dados sobre violência em outro.

Ao tentar unir as diferentes tabelas, dois tipos de problemas surgem:

- tabelas desse tipo raramente são completas, o que significa que países presentes numa lista podem não estar presentes na outra, e vice-versa;
- o nome de um país pode ser escrito de diferentes formas. "Brasil" pode ser também Brazil ou República Federativa do Brasil, dependendo de onde encontramos os dados. "Estados Unidos" pode ser também EUA, USA, United States, United States of America, Estados Unidos da América...

Essas duas questões, quando aplicadas a uma lista com 100, 200 países, e até mais, significa não só um trabalho monótono de identificar e ajustar as diferenças, mas também uma chance de cometermos erros.

Assim, o objetivo deste projeto é ajudar nesse processo.

1. As tabelas devem ser salvas no formato csv separado por tabulações (também chamado tsv), sem aspas, e enviadas ao site (botão Enviar arquivo).
1. Uma das colunas da tabela deve ser ligada a uma das colunas disponíveis no site (clique em Países para ver as colunas disponíveis).
1. Mais de uma coluna pode ser ligada ao mesmo tempo, embora não seja recomendado (nem lembro porque criei essa opção, mas deve ter um motivo).
1. Clique em Verificar.
1. Surgirão campos para identificar a URL de origem dos dados, outros metadados que aparecerão junto com a tabela na tela principal (descrição da tabela e das colunas, etc), o nome com que a tabela será salva no sistema, nomes alternativos para cada coluna (nomes sucintos, sem espaços ou caracteres especiais), bem como o tipo de dados de cada coluna (já pré-identificados). Além desses campos, aqueles países que não tiverem sido identificados com a base existente poderão ser identificados manualmente, inseridos ou ignorados. Dica: se quiser ignorar ou inserir vários países sem conferir, o teclado será bem mais rápido que o mouse.
1. Após preenchidos todos os campos, clique em Adicionar. Informações sobre o processo aparecerão na tela. Depois da próxima atualização, a tabela inserida estará junto às demais, se tudo correr razoavelmente bem (alguns erros podem acontecer, sem invabilizarem o processo).
