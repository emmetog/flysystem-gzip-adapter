<?php
namespace GzipAdapter;

use League\Flysystem\AdapterInterface;
use League\Flysystem\Config;

class Gzip implements AdapterInterface
{
  // Variables
  private $adapter;
  
  // Constructor
  public function __construct(AdapterInterface $adapter)
  {
    $this->adapter = $adapter;
  }
  
  // Check whether a file exists
  public function has($path)
  {
    return $this->adapter->has($path);
  }
  
  // Read a file
  public function read($path)
  {
    // Read the file from the adapter
    $read = $this->adapter->read($path);
    $readContents = $read['contents'];
    
    // Decode the contents
    $contents = gzdecode($readContents);
    
    return ['type' => 'file', 'path' => $path, 'contents' => $contents];
  }

  // Read a file as a stream
  public function readStream($path)
  {
    // Read the contents from the adapter and decode them
    $read = $this->read($path);
    $readContents = $read['contents'];
    
    // Create a new stream with the decoded contents
    $stream = fopen("php://temp","wb");
    fwrite($stream,$readContents);
    rewind($stream);
    
    return ['type' => 'file', 'path' => $path, 'stream' => $stream];
  }
  
  // List contents of a directory
  public function listContents($directory = '', $recursive = false): array
  {
    return $this->adapter->listContents($directory,$recursive);
  }

  // Get all the meta data of a file or directory
  public function getMetadata($path)
  {
    return $this->adapter->getMetadata($path);
  }
  
  // Get the size of a file
  public function getSize($path)
  {
    // TODO: Implement getSize
    return $this->adapter->getSize($path);
  }

  // Get the mimetype of a file
  public function getMimetype($path)
  {
    // TODO: Implement getMimetype
    return $this->adapter->getMimetype($path);
  }

  // Get the last modified time of a file as a timestamp
  public function getTimestamp($path)
  {
    return $this->adapter->getTimestamp($path);
  }

  // Get the visibility of a file
  public function getVisibility($path)
  {
    return $this->adapter->getVisibility($path);
  }

  // Write a new file
  public function write($path, $contents, Config $config)
  {
    // Encode the contents
    $writeContents = gzencode($contents,$config->get('compression',-1));
    
    // Write the contents to the adapter
    return $this->adapter->write($path,$writeContents,$config);
  }

  // Write a new file using a stream
  public function writeStream($path, $resource, Config $config)
  {
    // Read the contents of the stream
    $writeContents = stream_get_contents($resource);
    
    // Encode the contents and write them to the adapter
    return $this->write($path,$writeContents,$config);
  }
  
  // Update a file
  public function update($path, $contents, Config $config)
  {
    // Encode the contents
    $writeContents = gzencode($contents,$config->get('compression',-1));
    
    // Update the contents to the adapter
    return $this->adapter->update($path,$writeContents,$config);
  }

  // Update a file using a stream
  public function updateStream($path, $resource, Config $config)
  {
    // Read the contents of the stream
    $writeContents = stream_get_contents($resource);
    
    // Encode the contents and update them to the adapter
    return $this->update($path,$writeContents,$config);
  }

  // Rename a file
  public function rename($path, $newpath): bool
  {
    return $this->adapter->rename($path,$newpath);
  }
  
  // Copy a file
  public function copy($path, $newpath): bool
  {
    return $this->adapter->copy($path,$newpath);
  }
  
  // Delete a file
  public function delete($path): bool
  {
    return $this->adapter->delete($path);
  }

  // Delete a directory
  public function deleteDir($dirname): bool
  {
    return $this->adapter->deleteDir($dirname);
  }
  
  // Create a directory
  public function createDir($dirname, Config $config)
  {
    $this->adapter->createDir($dirname,$config);
  }
  
  // Set the visibility for a file
  public function setVisibility($path,$visibility)
  {
    $this->adapter->setVisibility($path,$visibility);
  }
}
