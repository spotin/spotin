import { Body, Controller, Get, Param, Res } from '@nestjs/common';
import {
  ApiOperation,
  ApiOkResponse,
  ApiTags,
  ApiParam,
  ApiNotFoundResponse,
} from '@nestjs/swagger';
import { Response } from 'express';
import { Spot, User } from '@prisma/client';
import { CreateSpotDto } from '@/spots/dtos/create-spot.dto';
import { UpdateSpotDto } from '@/spots/dtos/update-spot-type.dto';
import { SpotsService } from '@/spots/spots.service';
import { ReadSpotDto } from '@/spots/dtos/read-spot.dto';
import { CustomPost } from '@/common/decorators/custom-post.decorator';
import { GetOne } from '@/common/decorators/get-one.decorator';
import { GetMany } from '@/common/decorators/get-many.decorator';
import { CustomPatch } from '@/common/decorators/custom-patch.decorator';
import { CustomDelete } from '@/common/decorators/custom-delete.decorator';
import { AuthUser } from '@/auth/decorators/auth-user.decorator';
import { JwtOrTokenAuth } from '@/auth/jwt-or-token/jwt-or-token-auth.decorators';

@ApiTags('Spots')
@Controller('api/spots')
export class SpotsApiController {
  constructor(private readonly spotsService: SpotsService) {}

  @GetMany({
    path: 'public',
    name: 'Spots',
    summary: 'Get the public spots',
    operationId: 'getPublicSpotsApi',
    responseType: [ReadSpotDto],
  })
  async getPublicSpotsApi() {
    const spots = await this.spotsService.getPublicSpots();

    const spotsDto = spots.map((spot) => new ReadSpotDto(spot));

    return spotsDto;
  }

  @Get(':id/redirect')
  @ApiOperation({
    summary: 'Redirect to the link specified by the spot',
    description: 'Redirect to the link specified by the spot.',
    operationId: 'getSpotRedirection',
  })
  @ApiParam({
    name: 'id',
    description: 'The spot ID.',
    format: 'uuid',
  })
  @ApiOkResponse({
    description: 'Redirection success.',
    type: ReadSpotDto,
  })
  @ApiNotFoundResponse({
    description: 'Spot has not been found.',
  })
  async getSpotRedirection(@Res() res: Response, @Param('id') id: string) {
    const spot = (await this.spotsService.getSpot(id)) as Spot;

    if (!spot.configured) {
      res.redirect(`/spots/${id}/edit`);
    }

    if (spot?.redirection) {
      res.redirect(spot.redirection);
    }
  }

  @GetMany({
    name: 'Spots',
    summary: 'Get the spots',
    operationId: 'getSpotsApi',
    responseType: [ReadSpotDto],
  })
  @JwtOrTokenAuth()
  async getSpotsApi(@AuthUser() user: User) {
    const spots = await this.spotsService.getSpots(user);

    const spotsDto = spots.map((spot) => new ReadSpotDto(spot));

    return spotsDto;
  }

  @GetOne({
    name: 'Spot',
    summary: 'Get the specified spot',
    operationId: 'getSpotApi',
    responseType: ReadSpotDto,
  })
  @JwtOrTokenAuth()
  async getSpotApi(@AuthUser() user: User, @Param('id') id: string) {
    const spot = await this.spotsService.getSpot(id, user);

    return new ReadSpotDto(spot);
  }

  @CustomPost({
    name: 'Spot',
    summary: 'Create a new spot',
    bodyType: CreateSpotDto,
    responseType: ReadSpotDto,
    operationId: 'createSpotApi',
  })
  @JwtOrTokenAuth()
  async createSpotApi(
    @AuthUser() user: User,
    @Body() createSpotDto: CreateSpotDto,
  ) {
    const newSpot = await this.spotsService.createSpot(createSpotDto, user);

    return new ReadSpotDto(newSpot);
  }

  @CustomPatch({
    name: 'Spot',
    summary: 'Update the specified spot',
    bodyType: UpdateSpotDto,
    responseType: ReadSpotDto,
    operationId: 'updateSpotApi',
  })
  async updateSpotApi(
    @AuthUser() user: User,
    @Param('id') id: string,
    @Body() updateSpot: UpdateSpotDto,
  ) {
    const updatedSpot = await this.spotsService.updateSpot(
      id,
      updateSpot,
      user,
    );

    return new ReadSpotDto(updatedSpot);
  }

  @CustomDelete({
    name: 'Spot',
    summary: 'Delete the specified spot',
    operationId: 'deleteSpotApi',
  })
  @JwtOrTokenAuth()
  async deleteSpotApi(@AuthUser() user: User, @Param('id') id: string) {
    await this.spotsService.deleteSpot(id, user);
  }
}
