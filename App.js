import React, { Component } from 'react';
import { AppRegistry, StyleSheet, Text, View, Image, Button, Alert } from 'react-native';



export default class LotsOfStyles extends Component {
  handleEvent() {
    const fiftyFifty = Math.random() < 0.5;
    if (fiftyFifty == true) {
      Alert.alert('You are cool!');
    } else {
      Alert.alert('You are awesome!');
    }
    
   }
  render() {
   
    return (
      <View>
        <Text style={{marginTop:50}}></Text>
        <Text style={styles.bigpurple}>Welcome</Text>
        <Text style={styles.red}>Hi, it's Syfa</Text>
        <Image
          style={{width: 500, height: 400, alignSelf:'center', marginTop:30}}
          source={{uri: 'https://upload.wikimedia.org/wikipedia/commons/b/b2/Endangered_Red_Panda.jpg'}}
        />
        <Text style={styles.red}>Click this button again and again for a 50% chance of getting a different result:</Text>
        <View style={styles.buttonContainer}>
          <Button onPress={this.handleEvent} title="Click Me" />
        </View>
      </View>
    );
  }
}

const styles = StyleSheet.create({
  bigpurple: {
    color: 'purple',
    fontWeight: 'bold',
    fontSize: 30,
    textAlign: 'center',
  },
  red: {
    color: 'red',
    textAlign: 'center',
    marginTop:20,
  },
  buttonContainer: {
    backgroundColor: '#2E9298',
    borderRadius: 10,
    padding: 10,
    width: 300,
    marginTop:20,
    alignSelf: 'center',
    shadowColor: '#000000',
    shadowOffset: {
      width: 0,
      height: 3
    },
    shadowRadius: 10,
    shadowOpacity: 0.25
  }
});


AppRegistry.registerComponent('AwesomeProject', () => LotsOfStyles);