import React, { Component } from 'react';
import { AppRegistry, StyleSheet, Text, View } from 'react-native';

export default class LotsOfStyles extends Component {
  render() {
    return (
      <View>
        
        <Text style={styles.bigpurple}>Welcome</Text>
        <Text style={styles.bigpurple}></Text>
        
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
  },
});



// skip this line if using Create React Native App
AppRegistry.registerComponent('AwesomeProject', () => LotsOfStyles);