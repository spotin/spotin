import { Injectable } from '@nestjs/common';
import { randomUUID } from 'crypto';
import { PrismaService } from 'src/prisma/prisma.service';
import { CreateSpot } from './types/create-spot-type';
import { Spot } from './types/spot.type';

@Injectable()
export class SpotsService {
  constructor(private readonly prisma: PrismaService) {}

  async getSpots() {
    return await this.prisma.spots.findMany();
  }

  async createSpot(createSpot: CreateSpot) {
    return await this.prisma.$queryRawUnsafe<Spot>(
      'INSERT INTO spots(uuid,title,description,coordinates,timestamp,redirection,referenced) VALUES ($1::uuid, $2, $3, ST_MakePoint($4,$5)::geometry, $6::timestamp, $7, $8);',
      randomUUID(),
      createSpot.title,
      createSpot.description,
      createSpot.longitude,
      createSpot.latitude,
      createSpot.timestamp,
      createSpot.redirect,
      createSpot.referenced,
    );
  }
}
