import { Injectable } from '@nestjs/common';
import { PrismaService } from 'src/prisma/prisma.service';
import { CreateSpot } from './types/create-spot-type';

@Injectable()
export class SpotsService {
  constructor(private readonly prisma: PrismaService) {}

  async getSpots() {
    return await this.prisma.spots.findMany();
  }

  async createSpot(createSpot: CreateSpot) {
    const newSlideshow = await this.prisma.spots.create({
      data: {
        title: createSpot.title,
        description: createSpot.description,
        timestamp: createSpot.timestamp,
        redirection: createSpot.redirection,
        referenced: createSpot.referenced,
      },
    });

    return newSlideshow;
  }
}
