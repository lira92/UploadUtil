<?php
namespace lyft\Upload;

use Cake\Filesystem\File;
use Cake\Filesystem\Folder;
use Cake\Utility\Inflector;
use Cake\Log\Log;

class Upload
{
    /**
     * Organiza o upload.
     * @param array $imagem
     * @param string $dir
     * @return mixed
     * @throws NotImplementedException
     */
    public static function upload($imagem = array(), $dir = 'img')
	{
		$dir = WWW_ROOT.$dir.DS; 
		if(($imagem['error']!=0) and ($imagem['size']==0)) { 
			throw new NotImplementedException('Alguma coisa deu errado, o upload retornou erro '.$imagem['error'].' e tamanho '.$imagem['size']); 
		}
		Upload::checa_dir($dir);
		$imagem = Upload::checa_nome($imagem, $dir);
		Upload::move_arquivos($imagem, $dir);
		return $imagem['name']; 
	}


    /**
     * @param $dir
     */
    public static function checa_dir($dir)
	{
		$folder = new Folder(); 
		if (!is_dir($dir)) { 
			$folder->create($dir); 
		} 
	}

    /**
     * Verifica se o nome do arquivo já existe, se existir adiciona um numero ao nome e verifica novamente
     * @param $imagem
     * @param $dir
     * @return mixed
     */
    public static function checa_nome($imagem, $dir)
    {
        $imagem_info = pathinfo($dir . $imagem['name']);
        $imagem_nome = Upload::trata_nome($imagem_info['filename']) . '.' . $imagem_info['extension'];
        $conta = 2;
        while (file_exists($dir . $imagem_nome)) {
            $imagem_nome = Upload::trata_nome($imagem_info['filename']) . '-' . $conta;
            $imagem_nome .= '.' . $imagem_info['extension'];
            $conta++;
        }
        $imagem['name'] = $imagem_nome;
        return $imagem;
    }

    /**
     * Trata o nome removendo espaços, acentos e caracteres em maiúsculo.
     * @param $imagem_nome
     * @return string
     */
    public static function trata_nome($imagem_nome)
    {
        $imagem_nome = strtolower(Inflector::slug($imagem_nome, '-'));
        return $imagem_nome;
    }

    /**
     * Move o arquivo para a pasta de destino.
     * @param $imagem
     * @param $dir
     */
    public static function move_arquivos($imagem, $dir)
	{ 
		$arquivo = new File($imagem['tmp_name']); 
		$arquivo->copy($dir.$imagem['name']); $arquivo->close(); 
	}

    /**
     * @param $arquivo que será deletado, deve ser passado o path a partir do webroot.
     */
    public static function delete_arquivo($arquivo)
    {
        $dir = WWW_ROOT;
        $arquivo = new File($dir.DS.$arquivo);
        if($arquivo->exists())
        {
            $arquivo->delete();
        }
    }
}